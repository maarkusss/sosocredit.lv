<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {
    }

    public function build()
    {
        return $this->subject(__('E-pasta verifikācijas kods') . ' - ' . config('app.name'))
            ->html('
                <div style="font-family: Arial, sans-serif; padding: 20px; color: #333; max-width: 500px; margin: 0 auto; border: 1px solid #e0e0e0; rounded: 8px;">
                    <h2 style="color: #2E7D32; text-align: center;">' . config('app.name') . '</h2>
                    <p style="font-size: 16px;">Labdien!</p>
                    <p style="font-size: 16px;">Jūsu profila apstiprināšanas verifikācijas kods ir:</p>
                    <div style="background-color: #f4fbf5; border: 2px dashed #2E7D32; padding: 15px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1B5E20; margin: 20px 0;">
                        ' . $this->code . '
                    </div>
                    <p style="font-size: 14px; color: #666;">Kods ir derīgs 15 minūtes. Ja neesat pieprasījis šo kodu, lūdzu, ignorējiet šo e-pastu.</p>
                </div>
            ');
    }
}
