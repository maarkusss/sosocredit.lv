<?php

namespace App\Http\Controllers\Api;

use App\Contracts\HoiFmServiceInterface;
use App\Events\ConsumerRequiresSmsVerificationCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserJsonObjectModel;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerDevice;
use App\Models\Consumer\ConsumerSMSVerificationCode;
use App\Services\ConsumerService;
use App\Traits\WorksWithLanguageLocale;
use Goodday\Goodwall\Services\GoodwallService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use stdClass;

class AppUserController extends Controller
{
    use WorksWithLanguageLocale;

    protected string $locale;
    
    public function __construct(protected ConsumerService $consumer_service, protected HoiFmServiceInterface $hoi_fm,) {
        $country_locale = $this->getLanguageLocaleFromHeaders();

        if (!empty($country_locale)) {
            app()->setLocale($country_locale);
        }

        $this->locale = $country_locale ?? app()->getLocale();
    }

   /**
     * GET /api/user
     *
     */
    public function getUser(): JsonResponse
    {
        $app_instance_id = request()->header('App-Instance-Id');
        $app_instance_id = (string) $app_instance_id;

        if (empty($app_instance_id)) {
            return $this->errorResponse(['app_instance_id_invalid'], 404);
        }

        $device = $this->consumer_service->getDevice($app_instance_id);

        if (empty($device->id)) {
            return $this->errorResponse(['app_instance_id_invalid'], 404);
        }

        if (auth('sanctum')->check()) {
            $consumerAuth = auth('sanctum')->user();
    
            $device->consumer_id = $consumerAuth->id;
            $device->save();
        }

        $consumer = $this->consumer_service->getConsumer($device->consumer_id);

        if (empty($consumer->id)) {
            return $this->errorResponse(['consumer_invalid'], 401);
        }

        return $this->userResponse($consumer);
    }

    public function postNotificationClick(Request $request)
    {
        $app_instance_id = $request->app_instance_id;
        $push_notification_url = !empty($request->push_notification_url) ? $request->push_notification_url : '';
        $status = -1;

        if (!empty($app_instance_id)) {
            if (config('offers-api.log_notification_clicks') === true) {
                Log::debug('[Offers-api]: Click info - ', [
                    'app_instance_id' => $app_instance_id,
                    'push_notification_url' => $push_notification_url ?? '',
                ]);
            }

            if (config('offers-api.export_notification_clicks') === true) {
                $device = ConsumerDevice::query()->where('key', $app_instance_id)->first();
                if (!empty($device->id)) {
                    /** @var ConsumerDevice $device */
                    $status = $this->hoi_fm->resetPushDevice($device, $push_notification_url);
                }
            }
        }

        return new JsonResponse([
            'status' => $status,
            'data' => [
                'user' => [
                    'devices' => [
                        [
                            'app_instance_id' => $app_instance_id,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * POST /api/device-country-select
     */
    public function postDevice(Request $request): ?JsonResponse
    {
        $app_instance_id = request()->header('App-Instance-Id');
        $app_instance_id = (string) $app_instance_id;

        if (empty($app_instance_id)) {
            return $this->errorResponse(['app_instance_id_invalid'], 404);
        }

        $device = $this->consumer_service->getDevice($app_instance_id);

        if ($app_instance_id) {
            if (empty($device->id)) {
                $device = new ConsumerDevice();
                $device->key = $app_instance_id;
                $device->provider = ConsumerDevice::PROVIDER_FIREBASE;
                $device->user_agent = RequestFacade::userAgent();
            }
    
            if (!empty($request->fcm_token)) {
                $device->value = $request->fcm_token;
            }

            if (!empty($request->selected_country)) {
                $device->country = strtolower($request->selected_country);
            }

            // To do is check if country from project doesn't match the new selected country and if so, remove the device from hoi fm and re-add with new country. 
            // But for now, we will just update the country in the device and hoi fm will pick it up in the next sync.
            // if ($device->isDirty('country')) {
            //     if (!empty($previous_country)) {
            //         $this->hoi_fm->destroyDevice($device, $previous_country);
            //     }
            // }
        }

        $device->save();
        $device->touch();

        $this->hoi_fm->upsertPushDevice($device);

        return new JsonResponse([
            'data' => new stdClass(),
        ]);
    }

     /**
     * POST /api/user in CP
     */
    public function postUser(Request $request): ?JsonResponse
    {
        $app_instance_id = request()->header('App-Instance-Id');
        $app_instance_id = (string) $app_instance_id;

        if (empty($app_instance_id)) {
            return $this->errorResponse(['app_instance_id_invalid'], 404);
        }

        $device = $this->consumer_service->getDevice($app_instance_id);

        if ($app_instance_id) {
            if (empty($device->id)) {
                $device = new ConsumerDevice();
                $device->key = $app_instance_id;
                $device->provider = ConsumerDevice::PROVIDER_FIREBASE;
                $device->user_agent = RequestFacade::userAgent();
            }
    
            if (!empty($request->fcm_token)) {
                $device->value = $request->fcm_token;
            }

            // Used to rotate push projects
            // $previous_country = $device->country;
            if ($request->header('Country')) {
                $device->country = trim(strtolower($request->header('Country')));
            }
        }

        $consumer = $this->consumer_service->getConsumer($device->consumer_id);
        
        if (empty($consumer->id)) {
            return $this->errorResponse(['consumer_invalid'], 401);
        }

        $device->save();
        $device->touch();

        return $this->userResponse($consumer);
    }

    public function deleteUser(Request $request): ?JsonResponse
    {
        $consumer = $request->user();
        $consumer->tokens()->delete();
    
        return new JsonResponse([
            'data' => new stdClass(),
        ]);
    }

    /**
     * POST /api/user/authentication
     *
     * Initiate authentication process
     */
    public function authentication(Request $request)
    {
        if ($request->filled('phone_number')) {
            $phoneNumber = preg_replace('/\D+/', '', trim($request->input('phone_number')));
    
            $request->merge([
                'phone_number' => $phoneNumber,
            ]);
    
            if ($phoneNumber !== env('API_TEST_PHONE_NUMBER') && !preg_match('/^2\d{7}$/', $phoneNumber)) {
                return $this->errorResponse(['phone_number_format_invalid'], 404);
            }
        }
        
        $validated = $request->validate([
            'authentication_type' => ['required', 'string', 'max:15', 'in:email,phone_number'],
            'email' => ['required_if:authentication_type,email', 'nullable', 'email', 'max:191'],
            'phone_number' => ['required_if:authentication_type,phone_number', 'nullable', 'string', 'max:20'],
        ]);

        $consumer = $this->consumer_service->getFromRequest($request);

        if (!empty($consumer->id)) {
            $consumer = $this->consumer_service->update($consumer, $request, 'app');
        } else{
            $consumer = $this->consumer_service->create($request, 'app');
        }

        $recentSms = ConsumerSMSVerificationCode::where('phone_number', $consumer->phone)
            ->where('created_at', '>', now()->subSeconds(3))
            ->exists();
            
        if (!$recentSms) {
            event(new ConsumerRequiresSmsVerificationCode($consumer, resend: false));
        }

        return new JsonResponse([
            'data' => new stdClass(),
        ]);
    }


    /**
     * POST /api/user/authentication/complete
     *
     * Complete Consumer authentication process, compare $verification_code
     */
    public function authenticationComplete(Request $request)
    {
        $validated = $request->validate([
            'authentication_type' => ['required', 'string', 'max:15', 'in:email,phone_number'],
            'verification_code' => ['required', 'string', 'min:5', 'max:191'],
            'phone_number' => ['required_if:authentication_type,phone_number', 'nullable', 'string', 'max:20'],
        ]);

        if (!ConsumerSMSVerificationCode::query()
            ->where('phone_number', $validated['phone_number'])
            ->where('code', $validated['verification_code'])
            ->where('expires_at', '>=', date('Y-m-d H:i:s'))
            ->exists()) {

            return $this->errorResponse(['verification_code_invalid'], 404);
        }

        $consumer = $this->consumer_service->getFromRequest($request);

        return $this->userRegisteredResponse($consumer);
    }

    public function getAuthenticationType(){
        return new JsonResponse([
            'data' => [
                'authentication_type' => config('api.verification_type'),
            ]
        ]);
    }

    /*
    * App refresh token
    */
    public function appRefreshToken(Request $request): JsonResponse
    {
        $oldAccessToken = $request->user()
            ->tokens()
            ->where('name', 'access_token')
            ->where('token', $this->getTokenValue($request->old_access_token))
            ->first();

        if (!$oldAccessToken) {
            abort(401, 'Old access token not found');
        }

        $request->user()->currentAccessToken()->delete(); // Delete refresh token
        $oldAccessToken->delete(); // Delete old access token

        return response()->json([
            'data' => [
                'tokens' => $this->generateTokens($request->user(), $request),
            ],
        ]);
    }

     /**
     * Unified response for Consumer+ConsumerDevice to be used in all endpoints
     */
    protected function userResponse(Consumer $consumer): JsonResponse
    {
        return new JsonResponse([
            'data' => [
                'user' => (new UserJsonObjectModel($consumer))
                    ->additional(['show_fcm_token' => $this->shouldShowFcmToken()])
                    ->toArray(request()),
            ]
        ]);
    }

     /**
     * Unified response for Consumer+ConsumerDevice to be used in all endpoints
     */
    protected function userRegisteredResponse(Consumer $consumer): JsonResponse
    {
        return new JsonResponse([
            'data' => [
                'tokens' => $this->generateTokens($consumer),
                'user' => (new UserJsonObjectModel($consumer))
                    ->additional(['show_fcm_token' => $this->shouldShowFcmToken()])
                    ->toArray(request()),
            ]
        ]);
    }

    private function shouldShowFcmToken(): bool
    {
        if (!config('offers-api.uses_goodwall')) {
            return false;
        }

        $goodwall = App::make(GoodwallService::class);
        return $goodwall->isEnabled() && $goodwall->isIpWhitelisted(request()->ip());
    }

    private function generateTokens($consumer): array
    {
        return [
            'access_token' => $consumer->createToken('access_token', ['general-access'], now()->addHours(3))->plainTextToken,
            'refresh_token' => $consumer->createToken('refresh_token', ['refresh-token'], now()->addMonths(6))->plainTextToken,
        ];
    }

     /*
     * App get token value
     */
    private function getTokenValue(?string $fullToken): string
    {
        // Split the token by the '|' character and get the second part
        $parts = explode('|', $fullToken);
        $tokenWithoutPrefix = $parts[1] ?? null;

        return hash('sha256', $tokenWithoutPrefix);
    }

    protected function errorResponse(?array $errors_bag = [], int $status = 422): JsonResponse
    {
        $errors = [];
        foreach ($errors_bag as $error_key) {
            $errors[] = ['error_key' => $error_key];
        }

        return new JsonResponse(['errors' => $errors], $status);
    }
}
