<?php

namespace App\Http\Controllers;

use App\Events\ApplicationEmailVerified;
use App\Events\ApplicationPhoneVerified;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerEmailVerificationRequest;
use App\Models\Consumer\ConsumerPhoneVerificationRequest;
use App\Services\OfferPage\OfferPageService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;

class ConsumerVerificationRequestController extends Controller
{
    public function __construct(protected OfferPageService $offer_page_service)
    {

    }

    /**
     * Mark the consumer email address as verified.
     *
     * @return Renderable
     */
    public function email(): Renderable
    {
        $email_verification_request = ConsumerEmailVerificationRequest::find(request()->route('id'));
        /** @var ConsumerEmailVerificationRequest $email_verification_request */

        if (!empty($email_verification_request->email_verified_at)) {
            return $this->redirectConsumer($email_verification_request->consumer, [
                'email_verified' => 'true',
            ]);
        }

        if ($email_verification_request = $email_verification_request->markEmailAsVerified()) {
            event(new ApplicationEmailVerified($email_verification_request->application));
        }

        return $this->redirectConsumer($email_verification_request->consumer, [
            'email_verified' => 'true',
        ]);
    }

    /**
     * Mark the consumer phone number as verified.
     *
     * @return Renderable
     */
    public function phone(): Renderable
    {
        $phone_verification_request = ConsumerPhoneVerificationRequest::find(request()->route('id'));
        /** @var ConsumerPhoneVerificationRequest $phone_verification_request */

        if ($phone_verification_request->sms_verification_hash !== request()->route('hash')) {
            abort(400, 'Link is incorrect');
        }

        if (!empty($phone_verification_request->phone_verified_at)) {
            return $this->redirectConsumer($phone_verification_request->consumer, [
                'phone_verified' => 'true',
            ]);
        }

        if ($phone_verification_request = $phone_verification_request->markPhoneAsVerified()) {
            event(new ApplicationPhoneVerified($phone_verification_request->application));
        }

        return $this->redirectConsumer($phone_verification_request->consumer, [
            'phone_verified' => 'true',
        ]);
    }

    public function smsVerificationHash(string $sms_verification_hash)
    {
        $phone_verification_request = ConsumerPhoneVerificationRequest::query()->where('sms_verification_hash', $sms_verification_hash)->first();
        /** @var ConsumerPhoneVerificationRequest $phone_verification_request */

        if (empty($phone_verification_request->id)) {
            abort(404);
        }

        if (!empty($phone_verification_request->expires_at) && $phone_verification_request->expires_at < now()) {
            abort(401, 'Link has expired');
        }

        if (!empty($phone_verification_request->phone_verified_at)) {
            return $this->redirectConsumer($phone_verification_request->consumer, [
                'phone_verified' => 'true',
            ]);
        }

        if ($phone_verification_request = $phone_verification_request->markPhoneAsVerified()) {
            event(new ApplicationPhoneVerified($phone_verification_request->application));
        }

        return $this->redirectConsumer($phone_verification_request->consumer, [
            'phone_verified' => 'true',
        ]);
    }

    protected function redirectConsumer(Consumer $consumer, ?array $params = []): RedirectResponse
    {
        $link = $this->offer_page_service->buildLink($consumer, $params);

        $expiry_time_in_minutes = 60 * 24 * 30;

        return redirect($link)
            ->withCookie(cookie('offer_redirect', true, $expiry_time_in_minutes));
    }
}
