<?php

namespace App\Models\Consumer;

use Database\Factories\ConsumerSMSVerificationCodeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $phone_number
 * @property $code
 * @property $expires_at
 * @property $created_at
 * @property $updated_at
 */
class ConsumerSMSVerificationCode extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'consumers__sms_verification_codes';

    protected static function newFactory(): Factory
    {
        return ConsumerSMSVerificationCodeFactory::new();
    }

    public static function getFromRequest(string $phone, string $verification_code): ?ConsumerSMSVerificationCode
    {
        return ConsumerSMSVerificationCode::query()
            ->where('phone_number', $phone)
            ->where('code', $verification_code)
            ->where('expires_at', '>=', date('Y-m-d H:i:s'))
            ->first();
    }
}
