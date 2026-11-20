<?php

namespace App\Mail;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewDocumentMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected Document $document)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = config('app.name') . ' - Augšupielādēts jauns dokuments';
        $to = 'dokumenti@sosocredit.lv';

        $this->subject($subject)
            ->to($to)
            ->from(config('mail.from.address'))
            ->replyTo(config('mail.from.address'))
            ->view('mail.new_document', [
                'download_link' => $this->document->download_link,
                'document' => $this->document,
            ]);

        $this->withSymfonyMessage(function ($message) {
            $message->getHeaders()->addTextHeader('X-Mailer', 'PHP/' . phpversion());
        });

        return $this;
    }
}
