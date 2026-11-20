<?php

namespace App\Console\Commands;

use App\Contracts\SendSmsInterface;
use App\Mail\SecondLenderOffer;
use App\Models\Application;
use App\Models\Consumer;
use App\Services\Sms\Messages\ApplicationSecondOfferLinkMessage;
use App\Traits\WorksWithCrons;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class SendSecondOfferLinksCommand extends Command
{
    use WorksWithCrons;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'second-offer:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out SecondLenderOffer links via Emails and SMS to Consumers';

    public function __construct(
        protected SendSmsInterface $sms_sender,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->isCronEnabled($this->signature)) {
            $this->comment('Cron not enabled - skip');
            return CommandAlias::SUCCESS;
        }

        $emails_limit = 15;
        $sms_limit = 5;

        $pending_emails = Consumer::query()
            ->whereNotNull('offer_email_sent_at')
            ->whereNull('second_offer_email_sent_at')
            ->where('offer_email_sent_at', '<=', now()->subHours(24))
            ->orderBy('id', 'ASC')
            ->limit($emails_limit)
            ->get();

        $pending_smss = Consumer::query()
            ->whereNotNull('offer_sms_sent_at')
            ->whereNull('second_offer_sms_sent_at')
            ->where('offer_sms_sent_at', '<=', now()->subHours(24))
            ->where('offer_sms_sent_at', '>=', now()->subHours(48))
            ->orderBy('id', 'ASC')
            ->limit($sms_limit)
            ->get();

        if ($pending_emails->count() < 1 && $pending_smss->count() < 1) {
            $this->info('No Pending consumers found - exit');
            return CommandAlias::SUCCESS;
        }

        $this->comment('Pending Emails: ' . $pending_emails->count());
        $this->comment('Pending SMSs: ' . $pending_smss->count());

        try {
            if ($pending_emails->count() > 0) {
                /** @var Consumer $consumer */
                foreach ($pending_emails as $consumer) {
                    $this->sendSecondLenderOfferEmail($consumer);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        try {
            if ($pending_smss->count() > 0) {
                /** @var Consumer $consumer */
                foreach ($pending_smss as $consumer) {
                    $this->sendSecondLenderOfferSmsLink($consumer);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return CommandAlias::SUCCESS;
    }

    protected function sendSecondLenderOfferEmail(Consumer $consumer): void
    {
        if (empty($consumer->offer_email_sent_at)) {
            return;
        }

        if (!empty($consumer->second_offer_email_sent_at)) {
            return;
        }

        $second_lender_offer = new SecondLenderOffer($consumer);

        Mail::to($consumer->email)
            ->send($second_lender_offer);

        $consumer->second_offer_email_sent_at = now();
        $consumer->save();

        $consumer->sendings()->create([
            'type' => 'email',
            'subject' => $second_lender_offer->generateSubject(),
        ]);
    }

    protected function sendSecondLenderOfferSmsLink(Consumer $consumer)
    {
        if (empty($consumer->offer_sms_sent_at)) {
            return;
        }

        if (!empty($consumer->second_offer_sms_sent_at)) {
            return;
        }

        $last_application = $consumer->applications()->orderBy('id', 'DESC')->first();

        if (empty($last_application->id)) {
            Log::warning('[SendSecondOfferLinksCommand]: Empty last application', [
                'consumer_id' => $consumer->id,
            ]);
            return;
        }

        /** @var Application $last_application */

        $second_offer_hash = $last_application->second_offer_hash;

        $offer_link_message = new ApplicationSecondOfferLinkMessage($second_offer_hash);

        $this->sms_sender->sendSms(
            country_code: config('app.country_code'),
            phone: $consumer->phone,
            alpha_name: $offer_link_message->sender, // This must be allowed by SMS Gateway (Sales.lv)
            message: $offer_link_message->message,
        );

        $consumer->second_offer_sms_sent_at = now();
        $consumer->save();

        $consumer->sendings()->create([
            'type' => 'sms',
            'subject' => $offer_link_message->sender,
            'message' => Str::length($offer_link_message->message) > 191 ? Str::substr($offer_link_message->message, 0, 191) : $offer_link_message->message,
        ]);
    }
}
