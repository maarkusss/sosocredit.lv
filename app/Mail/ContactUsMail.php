<?php

namespace App\Mail;

use App\Http\Requests\ContactFormRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected ContactFormRequest $request)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Contact Form from website';
        $to = 'info@' . request()->getHost();

        $this->subject($subject)
            ->to($to)
            ->from(config('mail.from.address'))
            ->replyTo($this->request->email)
            ->view('mail.contact_us', [
                'email' => $this->request->email,
                'name' => $this->request->name,
                'question' => nl2br(strip_tags($this->request->question)),
            ]);

        $this->withSymfonyMessage(function ($message) {
            $message->getHeaders()->addTextHeader('X-Mailer', 'PHP/' . phpversion());
        });

        return $this;
    }
}
