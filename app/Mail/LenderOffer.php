<?php

namespace App\Mail;

use App\Contracts\Consumerable;
use App\Entity\UtmDataDTO;
use App\Models\Lender;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LenderOffer extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $lenders = [];
    public Consumerable $user;

    public function __construct(Consumerable $user)
    {
        $lenders_collection = Lender::active()
            ->orderBy('position', 'ASC')
            ->get();

        $lenders = [];
        if ($lenders_collection->isNotEmpty()) {
            foreach ($lenders_collection as $lender_model) {
                $lenders[] = $lender_model;
            }
        }

        $utm_data = UtmDataDTO::fromConsumer($user);

        $this->lenders = Lender::filter($lenders, $utm_data);
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->generateSubject();
        $unsubscribe_link = config('app.url') . '/' . default_locale() . '/unsubscribe/email';

        $this->subject($subject)
            ->view('mail.lender_offer', [
                'unsubscribe_link' => $unsubscribe_link,
            ]);

        $this->withSymfonyMessage(function ($message) use ($unsubscribe_link) {
            $message->getHeaders()
                ->addTextHeader('List-Unsubscribe', '<' . $unsubscribe_link .'>');
        });

        return $this;
    }

    public function generateSubject(): string
    {
        return __('main.lenderoffer_subject') . ' ' . date('d-m-Y');
    }
}
