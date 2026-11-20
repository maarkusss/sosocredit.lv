<?php

namespace App\Models\Consumer;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerEmailVerificationCode extends Model
{
    use HasFactory;

    protected $table = 'consumer_email_verification_codes';

    protected $fillable = [
        'email',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Generate 6-digit code for email
     */
    public static function generateForEmail(string $email): self
    {
        $code = (string) rand(100000, 999999);

        // Delete old unused codes for this email
        self::where('email', $email)->delete();

        return self::create([
            'email' => strtolower(trim($email)),
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);
    }

    /**
     * Verify code for email
     */
    public static function verify(string $email, string $code): bool
    {
        $record = self::where('email', strtolower(trim($email)))
            ->where('code', trim($code))
            ->where('expires_at', '>=', Carbon::now())
            ->first();

        if ($record) {
            $record->delete();
            return true;
        }

        return false;
    }
}
