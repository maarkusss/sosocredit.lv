<?php

namespace App\Mail;

use App\Contracts\Consumerable;
use App\Entity\UtmDataDTO;
use App\Models\Lender;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * LenderOffer email to be sent first
 * Excludes specific campaigns
 */
class FirstLenderOffer extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $lenders = [];

    public Consumerable $user;

    protected string $generated_subject;

    public function __construct(Consumerable $user)
    {
        $lenders_collection = Lender::active()
            ->whereNotIn('campaign_id', config('app.first_lender_offer_exclude_campaign_ids'))
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
        if (empty($this->generated_subject)) {
            $random_subjects = [
                __('main.first_lenderoffer_subject_1'),
                __('main.first_lenderoffer_subject_2'),
                __('main.first_lenderoffer_subject_3'),
            ];

            $random_subject = $random_subjects[rand(0, count($random_subjects) - 1)];

            $this->generated_subject = $random_subject;
        }

        return $this->generated_subject;
    }
}
