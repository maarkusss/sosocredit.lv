<?php

namespace App\Models;

use App\Contracts\Consumerable;
use App\Models\Consumer\ConsumerConsent;
use App\Models\Consumer\ConsumerDevice;
use App\Models\Consumer\ConsumerSending;
use App\Models\Consumer\ConsumersHoiToken;
use App\Notifications\ConsumerVerifyEmail;
use Database\Factories\ConsumerFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property $id
 * @property $token
 * @property $fingerprint
 * @property $visitor_id
 * @property $first_name
 * @property $last_name
 * @property $birth
 * @property $email
 * @property $offer_email_sent_at
 * @property $second_offer_email_sent_at
 * @property $email_verified_at
 * @property $phone
 * @property $offer_sms_sent_at
 * @property $second_offer_sms_sent_at
 * @property $amount
 * @property $hoi_token
 * @property $term
 * @property $type
 * @property $confirmed
 * @property $confirmed_at // phone verification
 * @property $subscribed
 * @property $lang
 * @property $ip
 * @property $utm_source
 * @property $utm_medium
 * @property $utm_campaign
 * @property $utm_content
 * @property $gclid
 * @property $fbc
 * @property $fbp
 * @property $version
 * @property $created_at
 * @property $updated_at
 *
 * @property $full_name
 *
 * @method hasVerifiedEmail() comes from Illuminate\Auth\MustVerifyEmail
 */
class Consumer extends Authenticatable implements MustVerifyEmail, Consumerable
{
    use Notifiable;
    use HasFactory;
    use HasApiTokens;

    protected $guarded = [];
    protected $table = 'consumers';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function newFactory(): Factory
    {
        return ConsumerFactory::new();
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new ConsumerVerifyEmail());
    }

    public function markPhoneAsConfirmed(): Consumer
    {
        return $this->markPhoneAsVerified();
    }

    public function markPhoneAsVerified(): Consumer
    {
        $this->forceFill([
            'confirmed' => true,
            'confirmed_at' => $this->freshTimestamp(),
        ])->save();

        return $this;
    }

    public function markEmailAsVerified(): Consumer
    {
        $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();

        return $this;
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'consumer_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(ConsumerDevice::class, 'consumer_id');
    }

    public function devices_firebase(): HasMany
    {
        return $this->devices()
            ->where('provider', ConsumerDevice::PROVIDER_FIREBASE);
    }

    public function hoi_tokens(): HasMany
    {
        return $this->hasMany(ConsumersHoiToken::class, 'consumer_id');
    }

    public function sendings(): HasMany
    {
        return $this->hasMany(ConsumerSending::class, 'consumer_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Determine if the consumer has verified his phone number.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->confirmed_at);
    }

    /**
     * Determine if the consumer is subscribed to emails and SMS
     *
     * @return bool
     */
    public function isSubscribedToEmailsAndSms(): bool
    {
        return !empty($this->subscribed) || (!empty($this->subscribed_email) && !empty($this->subscribed_sms));
    }

    public function isSubscribedToEmail(): bool
    {
        return !empty($this->subscribed_email) || !empty($this->subscribed);
    }

    public function isSubscribedToSms(): bool
    {
        return !empty($this->subscribed_sms) || !empty($this->subscribed);
    }

    /**
     * Determine if the consumer has signed up via APP
     *
     * @return bool
     */
    public function hasSignedUpViaApp(): bool
    {
        return !empty($this->utm_source) && $this->utm_source === 'app';
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'consumer_id');
    }

    public function consents(): HasMany
    {
        return $this->hasMany(ConsumerConsent::class, 'consumer_id');
    }

    public function currentConsent(): HasOne
    {
        return $this->hasOne(ConsumerConsent::class)->ofMany(
            ['consented_at' => 'max', 'id' => 'max']
        );
    }

    public function createConsent(
        bool $terms,
        bool $marketing,
        string $language, // must be from getLocale, to ensure it's what he saw
        ?string $ip,
        ?string $user_agent
    ): void
    {
        $termsSection = Section::where('common_name', 'privacy-policy')
            ->where('lang', $language)
            ->first();
        $marketingSection = Section::where('common_name', 'marketing-policy')
            ->where('lang', $language)
            ->first();

        /** @var  ConsumerConsent|null $latest */
        $latest = $this->currentConsent;

        if ($latest
            && $latest->terms == $terms
            && $latest->marketing == $marketing
            && $latest->terms_snapshot === $termsSection?->text
            && $latest->marketing_snapshot === $marketingSection?->text
        ) { // nothing changed
            return;
        }

        if ( ! $termsSection) {
            Log::warning('Terms text retrieval failed for consumer: ' . $this->id);
        }
        if ( ! $marketingSection) {
            Log::warning('Marketing text retrieval failed for consumer: ' . $this->id);
        }

        $this->consents()->create([
            'terms' => $terms,
            'marketing' => $marketing,
            'user_agent' => $user_agent,
            'ip' => $ip,
            'consented_at' => now(),
            'terms_snapshot' => $termsSection?->text,
            'marketing_snapshot' => $marketingSection?->text,
        ]);
    }
}
