<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactUsMail;
use App\Models\ContactForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function post(ContactFormRequest $request): RedirectResponse
    {
        $is_recaptcha_valid = !config('recaptcha.enabled');

        if ($request->input('recaptcha')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, config('recaptcha.api_domain'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                "secret=" . config('recaptcha.secret_key')
                . "&remoteip=" . $_SERVER['REMOTE_ADDR']
                . "&response=" . $request->input('recaptcha')
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);

            $recaptcha = json_decode($server_output);

            $is_recaptcha_valid = $recaptcha && $recaptcha->success && $recaptcha->score >= 0.1;
        }

        $is_honeypot_empty = $request->has('answer') && is_null($request->input('answer'));

        if ($is_recaptcha_valid === true && $is_honeypot_empty === true && $this->shouldSendEmail()) {
            //            $this->sendViaLaravel($request);
            $this->sendViaMail($request);
        }

        $request->merge([
            'is_recaptcha_valid' => $is_recaptcha_valid,
            'is_honeypot_empty' => $is_honeypot_empty,
        ]);

        ContactForm::storeFromRequest($request);

        return redirect()
            ->back()
            ->with([
                'success' => __('main.message_sent_successfully'),
            ]);
    }

    protected function sendViaMail(ContactFormRequest $request)
    {
        $from = config('mail.from.address');
        $to = 'info@' . $request->getHost();
        $subject = 'Contact Form from website';
        $message = $request->input('question');
        $headers = 'From: ' . $from . "\r\n" .
            'Reply-To: ' . $request->input('email') . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers, '-f' . $from);
    }

    protected function sendViaLaravel(ContactFormRequest $request)
    {
        Mail::send(new ContactUsMail($request));
    }

    public function shouldSendEmail(): bool
    {
        return app()->isProduction();
    }
}
