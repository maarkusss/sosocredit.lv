<?php

namespace App\Services;

use App\Contracts\HoiFmServiceInterface;
use App\Entity\UtmDataDTO;
use App\Events\ConsumerRequiresEmailVerificationLink;
use App\Events\ConsumerRequiresSmsVerificationCode;
use App\Models\Application;
use App\Models\Consumer\ConsumerDevice;
use App\Models\Consumer;
use App\Models\Consumer\ConsumersHoiToken;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConsumerService
{
    public ?UtmDataDTO $utm_data;

    public function __construct(protected HoiFmServiceInterface $hoi_fm)
    {
        $this->utm_data = UtmDataDTO::fromAppRequest();
    }

    public function getByHoiToken(string $hoi_token): ?Consumer
    {
        $consumer_hoi_token = ConsumersHoiToken::query()
            ->where('hoi_token', $hoi_token)
            ->first();

        if ($consumer_hoi_token) {
            /** @var ConsumersHoiToken $consumer_hoi_token */
            $consumer_hoi_token->touchClickedDate();

            $consumer = Consumer::find($consumer_hoi_token->consumer_id);

            if ($consumer && $consumer->id) {
                /** @var Consumer */
                return $consumer;
            }
        }

        return null;
    }

    public function getFromRequest(Request $request): ?Consumer
    {
        $consumer = null;

        if ($request->has('phone') && $request->has('email')) {
            $consumer = Consumer::query()->where('phone', $request->phone)->first();
        } elseif ($request->has('phone')) {
            $consumer = Consumer::query()->where('phone', $request->phone)->first();
        } elseif ($request->has('phone_number')) {
            $consumer = Consumer::query()->where('phone', $request->phone_number)->first();
        } elseif ($request->has('contact_phone_number')) {
            $consumer = Consumer::query()->where('phone', $request->contact_phone_number)->first();
        } elseif ($request->has('contact_email')) {
            $consumer = Consumer::query()->where('email', $request->contact_email)->first();
        } elseif ($request->has('email')) {
            $consumer = Consumer::query()->where('email', $request->email)->first();
        }

        return $consumer;
    }

    public function create(Request $request, ?string $source = null): Consumer
    {
        $hoi_token = $this->hoi_fm->generateNewToken();

        $subscribed = $this->getSubscribedChoice($request);

        if (!empty($source) && ($source === 'android-app' || $source === 'ios-app')) {
            $email = $request->contact_email ?? $request->email ?? null;
            $phone = $request->contact_phone_number ?? $request->phone ?? $request->phone_number ?? null;

            $consumer = Consumer::create([
                'amount' => $request->amount,
                'email' => $email,
                'hoi_token' => $hoi_token,
                'ip' => $request->ip(),
                'lang' => app()->getLocale(),
                'phone' => $phone,
                'utm_source' => $this->utm_data->utm_source,
                'subscribed' => 0,
            ]);

            $app_instance_id = request()->header('App-Instance-Id');
            $app_instance_id = (string) $app_instance_id;
            $country = request()->header('Country');
            $country = (string) $country;

            if ($app_instance_id) {
                $updateData = [
                    'consumer_id' => $consumer->id,
                    'country' => $country ?? null,
                    'provider' => ConsumerDevice::PROVIDER_FIREBASE,
                    'user_agent' => request()->userAgent(),
                ];

                if (!empty($request->fcm_token)) {
                    $updateData['value'] = $request->fcm_token;
                }

                $device = ConsumerDevice::updateOrCreate(
                    ['key' => $app_instance_id],
                    $updateData
                );

                $this->hoi_fm->deletePushDevice($device);
            }

            $this->hoi_fm->upsertPushConsumer($consumer);
        } else {
            $consumer = Consumer::create([
                'amount' => $request->amount,
                'email' => $request->email ?: null,
                'fbc' => $request->cookie('_fbc'),
                'fbp' => $request->cookie('_fbp'),
                'fingerprint' => $request->cookie('fingerprint'),
                'first_name' => $request->name,
                'gclid' => $request->cookie('gclid'),
                'hoi_token' => $hoi_token,
                'ip' => $request->ip(),
                'lang' => app()->getLocale() ?? null,
                'phone' => $request->phone ?: null,
                'subscribed' => $subscribed,
                'term' => $request->term,
                'type' => formatLoanTypeName($request->type),
                'utm_source' => $request->cookie('utm_source'),
                'utm_campaign' => $request->cookie('utm_campaign'),
                'utm_medium' => $request->cookie('utm_medium'),
                'utm_content' => $request->cookie('utm_content'),
                'visitor_id' => $request->cookie('visitor_id'),
                'version' => 1,
            ]);
        }

        $consumer->hoi_tokens()->create([
            'hoi_token' => $hoi_token,
        ]);

        return $consumer;
    }

    public function update(Consumer $consumer, Request $request, ?string $source = null): Consumer
    {
        $hoi_token = $this->hoi_fm->generateNewToken();

        $subscribed = $this->getSubscribedChoice($request);

        if (!empty($source) && ($source === 'android-app' || $source === 'ios-app')) {
            $consumer->update([
                'hoi_token' => $hoi_token,
                'ip' => $request->ip(),
                'lang' => app()->getLocale(),
                'utm_source' => $this->utm_data->utm_source,
            ]);

            $app_instance_id = request()->header('App-Instance-Id');
            $app_instance_id = (string) $app_instance_id;
            $country = request()->header('Country');
            $country = (string) $country;

            if ($app_instance_id) {
                $updateData = [
                    'consumer_id' => $consumer->id,
                    'country' => $country ?? null,
                    'provider' => ConsumerDevice::PROVIDER_FIREBASE,
                    'user_agent' => request()->userAgent(),
                ];

                if (!empty($request->fcm_token)) {
                    $updateData['value'] = $request->fcm_token;
                }

                $device = ConsumerDevice::updateOrCreate(
                    ['key' => $app_instance_id],
                    $updateData
                );

                $this->hoi_fm->deletePushDevice($device);
            }

            $this->hoi_fm->upsertPushConsumer($consumer);
        } else {
            $consumer->update([
                'amount' => $request->amount,
                'fbc' => $request->cookie('_fbc'),
                'fbp' => $request->cookie('_fbp'),
                'gclid' => $request->cookie('gclid'),
                'fingerprint' => $request->cookie('fingerprint'),
                'first_name' => $request->name,
                'hoi_token' => $hoi_token,
                'lang' => app()->getLocale() ?? null,
                'subscribed' => $subscribed,
                'term' => $request->term,
                'type' => formatLoanTypeName($request->type),
                'utm_source' => $request->cookie('utm_source'),
                'utm_campaign' => $request->cookie('utm_campaign'),
                'utm_medium' => $request->cookie('utm_medium'),
                'utm_content' => $request->cookie('utm_content'),
                'visitor_id' => $request->cookie('visitor_id'),
                'version' => 1,
            ]);
        }

        $consumer->hoi_tokens()->create([
            'hoi_token' => $hoi_token,
        ]);

        return $consumer;
    }

    public function resetEmailVerificationIfRequired(Consumer $consumer, Request $request): void
    {
        $should_reverify_email = ($consumer->email == null && $request->email) ||
            (($consumer->email != null && $request->email != null) && ($consumer->email != $request->email));

        if ($should_reverify_email) {
            $consumer->update([
                'email_verified_at' => null,
                'email' => $request->email,
            ]);
        }
    }

    public function resetPhoneVerificationIfRequired(Consumer $consumer, Request $request): void
    {
        $should_reverify_phone = ($consumer->phone == null && $request->phone) ||
            (($consumer->phone != null && $request->phone != null) && ($consumer->phone != $request->phone));

        if ($should_reverify_phone) {
            $consumer->update([
                'confirmed' => false,
                'confirmed_at' => null,
                'phone' => $request->phone,
            ]);
        }
    }

    public function rotateHoiToken(Consumer $consumer): Consumer
    {
        $new_hoi_token = $this->hoi_fm->generateNewToken();

        $consumer->update([
            'hoi_token' => $new_hoi_token,
        ]);

        $consumer->hoi_tokens()->create([
            'hoi_token' => $new_hoi_token,
        ]);

        return $consumer;
    }

    public function exportContactDetails(Consumer $consumer): void
    {
        if ($consumer->email) {
            if (config('app.verify_email') && !$consumer->hasVerifiedEmail() && $consumer->isSubscribedToEmailsAndSms()) {
                event(new ConsumerRequiresEmailVerificationLink($consumer));
            }

            if ($this->hoi_fm->shouldExportEmailToHoi($consumer)) {
                $this->hoi_fm->reInsertConsumerEmail($consumer);
            }
        }

        if ($consumer->phone) {
            if (
                config('app.verify_phone') && !$consumer->hasVerifiedPhone() ||
                config('app.verify_phone_in_api') && $consumer->hasSignedUpViaApp() && !$consumer->hasVerifiedPhone()
            ) {
                event(new ConsumerRequiresSmsVerificationCode($consumer, resend: false));
            }

            if ($this->hoi_fm->shouldExportPhoneToHoi($consumer)) {
                $this->hoi_fm->reInsertConsumerPhone($consumer);
            }
        }
    }

    public function getSubscribedChoice(Request $request): bool
    {
        return config('landing_form.show_optional_subscribe') !== true || !empty($request->subscribe);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getAssociatedLoantype(Consumer $consumer): ?LoanType
    {
        return LoanType::query()
            ->where('lang', $consumer->lang)
            ->where('route_name', $consumer->type)
            ->first();
    }

    public function createFromApplication(Application $application): Consumer
    {
        $hoi_token = $this->hoi_fm->generateNewToken();

        $consumer = Consumer::create([
            'amount' => $application->amount,
            'email' => $application->email ?: null,
            'fingerprint' => $application->fingerprint,
            'first_name' => $application->first_name,
            'gclid' => $application->gclid,
            'hoi_token' => $hoi_token,
            'ip' => $application->ip,
            'lang' => $application->lang,
            'phone' => $application->phone ?: null,
            'subscribed' => $application->subscribed,
            'subscribed_email' => $application->subscribed_email ?? 0,
            'subscribed_sms' => $application->subscribed_sms ?? 0,
            'term' => $application->term_months . ' mon.',
            'type' => $application->loantype,
            'utm_source' => $application->utm_source,
            'utm_campaign' => $application->utm_campaign,
            'utm_medium' => $application->utm_medium,
            'utm_content' => $application->utm_content,
            'visitor_id' => request()->cookie('visitor_id'),
        ]);

        $consumer->hoi_tokens()->create([
            'hoi_token' => $hoi_token,
        ]);

        return $consumer;
    }

    public function getDevice(string $app_instance_id): ?ConsumerDevice
    {
        return ConsumerDevice::query()->where('key', $app_instance_id)->first();
    }

    public function getConsumer(int $consumer_id): ?Consumer
    {
        return Consumer::query()->where('id', $consumer_id)->first([
            'id',
            'phone',
            'email',
            'lang',
        ]);
    }
}
