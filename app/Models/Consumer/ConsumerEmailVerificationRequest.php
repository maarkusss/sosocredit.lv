<?php

namespace App\Models\Consumer;

use App\Models\BelongsToApplication;
use App\Notifications\ConsumerRequestVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property $id
 * @property $consumer_id
 * @property $application_id
 * @property $email
 * @property $email_verification_link_sent_at
 * @property $email_verified_at
 * @property $created_at
 * @property $updated_at
 */
class ConsumerEmailVerificationRequest extends Model
{
    use BelongsToApplication;
    use BelongsToConsumer;
    use Notifiable;

    protected $guarded = [];

    protected $table = 'consumers__email_verification_requests';

    public $timestamps = true;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new ConsumerRequestVerifyEmail());
    }

    public function markEmailAsVerified(): ConsumerEmailVerificationRequest
    {
        $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();

        return $this;
    }
}
