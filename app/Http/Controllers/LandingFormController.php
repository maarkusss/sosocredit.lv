<?php

namespace App\Http\Controllers;

use App\Events\ConsumerPhoneVerified;
use App\Events\ConsumerRequiresSmsVerificationCode;
use App\Events\ConsumerReSignup;
use App\Events\ConsumerSignup;
use App\Http\Requests\LandingFormRequest;
use App\Http\Requests\SendVerificationCodeAgainRequest;
use App\Http\Requests\VerifySmsVerificationCodeRequest;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Services\ConsumerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LandingFormController extends Controller
{
    public const POST_REDIRECT_URL = '/u/?is_button=true';
    public const POST_REDIRECT_TIMEOUT = 300; // in milliseconds

    public function __construct(protected ConsumerService $consumer_service)
    {
        override_locale_if_provided();
    }

    /**
     * Post the landing page form data to DB.
     *
     * @param LandingFormRequest $request
     * @return JsonResponse
     */
    public function post(LandingFormRequest $request): JsonResponse
    {
        $consumer = $this->consumer_service->getFromRequest($request);

        if ($consumer && $consumer->id) {
            $this->consumer_service->resetEmailVerificationIfRequired(consumer: $consumer, request: $request);
            $this->consumer_service->resetPhoneVerificationIfRequired(consumer: $consumer, request: $request);

            $consumer = $this->consumer_service->update(consumer: $consumer, request: $request);
            $this->consumer_service->exportContactDetails($consumer);

            event(new ConsumerReSignup($consumer));

            return response()
                ->json([
                    'message' => 'Consumer updated!',
//                    'redirect_url' => static::POST_REDIRECT_URL,
//                    'redirect_timeout' => static::POST_REDIRECT_TIMEOUT,
                    'verified' => [
                        'email' => $consumer->email_verified_at && $request->email,
                        'phone' => $consumer->confirmed_at && $request->phone,
                    ],
                ])
                ->withCookie(cookie('hoi_token', $consumer->hoi_token, 60 * 24 * 30));
        } else {
            $consumer = $this->consumer_service->create(request: $request);
            $this->consumer_service->exportContactDetails($consumer);

            event(new ConsumerSignup($consumer));

            return response()
                ->json([
                    'message' => 'Consumer created!',
//                    'redirect_url' => static::POST_REDIRECT_URL,
//                    'redirect_timeout' => static::POST_REDIRECT_TIMEOUT,
                    'verified' => [
                        'email' => false,
                        'phone' => false,
                    ],
                ])
                ->withCookie(cookie('hoi_token', $consumer->hoi_token, 60 * 24 * 30));
        }
    }

    /**
     * Verify an SMS verification code.
     *
     * @param VerifySmsVerificationCodeRequest $request
     * @return JsonResponse
     */
    public function verifySMSVerificationCode(VerifySmsVerificationCodeRequest $request): JsonResponse
    {
        $sms_verification_code = ConsumerSMSVerificationCode::getFromRequest(
            phone: $request->phone,
            verification_code: $request->verification_code
        );

        if (!$sms_verification_code) {
            return response()
                ->json([
                    'message' => __("Invalid verification code"),
                    'errors' => [
                        'verification_code' => [
                            0 => __("Invalid verification code"),
                        ],
                    ],
                    'data' => [
                        'phone' => $request->phone,
                        'code' => $request->verification_code,
                    ],
                ], Response::HTTP_BAD_REQUEST);
        }

        $consumer = $this->consumer_service->getFromRequest($request);

        if (!$consumer) {
            return response()
                ->api_response('Consumer not found!', Response::HTTP_NOT_FOUND);
        }

        if (!$consumer->hasVerifiedPhone()) {
            $consumer = $consumer->markPhoneAsVerified();
            event(new ConsumerPhoneVerified($consumer));
        }

        return response()
            ->api_response('Consumer verified!');
    }

    /**
     * Send the verification code SMS again.
     *
     * @param SendVerificationCodeAgainRequest $request
     * @return JsonResponse
     */
    public function sendVerificationCodeSMSAgain(SendVerificationCodeAgainRequest $request): JsonResponse
    {
        $consumer = $this->consumer_service->getFromRequest($request);

        if (!$consumer || !$consumer->id) {
            return response()
                ->api_response('Consumer not found!', Response::HTTP_NOT_FOUND);
        }

        $executed = RateLimiter::attempt(
            key: 'send-sms:' . $consumer->phone,
            maxAttempts: 1,
            callback: fn () => event(new ConsumerRequiresSmsVerificationCode($consumer, resend: true))
        );

        if (!$executed) {
            $seconds = RateLimiter::availableIn('send-sms:' . $consumer->phone);

            return response()
                ->json([
                    'message' => 'Please try again in: ' . $seconds . ' seconds',
                    'errors' => [
                        'verification_code' => [
                            0 => 'Please try again in: ' . $seconds . ' seconds',
                        ],
                    ],
                ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return response()
            ->api_response('Verification code sent again!');
    }
}
