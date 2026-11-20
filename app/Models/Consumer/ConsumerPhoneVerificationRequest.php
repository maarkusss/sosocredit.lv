<?php

namespace App\Models\Consumer;

use App\Models\BelongsToApplication;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $consumer_id
 * @property $application_id
 * @property $phone
 * @property $sms_verification_hash
 * @property $sms_verification_link_sent_at
 * @property $phone_verified_at
 * @property $expires_at
 * @property $created_at
 * @property $updated_at
 */
class ConsumerPhoneVerificationRequest extends Model
{
    use BelongsToApplication;
    use BelongsToConsumer;

    protected $guarded = [];

    protected $table = 'consumers__phone_verification_requests';

    public $timestamps = true;

    public function markPhoneAsVerified(): ConsumerPhoneVerificationRequest
    {
        $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();

        return $this;
    }
}
