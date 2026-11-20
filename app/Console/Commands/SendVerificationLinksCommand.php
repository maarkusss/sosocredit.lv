<?php

namespace App\Console\Commands;

use App\Contracts\SendSmsInterface;
use App\Models\Consumer;
use App\Models\Consumer\ConsumerEmailVerificationRequest;
use App\Models\Consumer\ConsumerPhoneVerificationRequest;
use App\Services\HoiFm\HoiFmService;
use App\Services\Sms\Messages\VerificationLinkMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class SendVerificationLinksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verifications:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out verification links via Email and SMS to Consumers';

    public function __construct(
        protected SendSmsInterface $sms_sender,
        protected HoiFmService $hoi_fm
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
        $limit = 5;

        $pending_email_requests = ConsumerEmailVerificationRequest::query()
            ->whereNull('email_verification_link_sent_at')
            ->whereNull('email_verified_at')
            ->orderBy('id', 'ASC')
            ->limit($limit)
            ->get();

        $pending_sms_requests = ConsumerPhoneVerificationRequest::query()
            ->whereNull('sms_verification_link_sent_at')
            ->whereNull('phone_verified_at')
            ->orderBy('id', 'ASC')
            ->limit($limit)
            ->get();

        if ($pending_sms_requests->count() < 1 && $pending_email_requests->count() < 1) {
            $this->info('No Pending verification requests found - exit');
            return CommandAlias::SUCCESS;
        }

        $this->comment('Pending Email Verifications: ' . $pending_email_requests->count());
        $this->comment('Pending SMS Verifications: ' . $pending_sms_requests->count());

        try {
            if ($pending_email_requests->count() > 0) {
                /** @var ConsumerEmailVerificationRequest $verification_request */
                foreach ($pending_email_requests as $email_verification_request) {
                    $this->sendEmailVerificationLink($email_verification_request);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        try {
            if ($pending_sms_requests->count() > 0) {
                /** @var ConsumerPhoneVerificationRequest $phone_verification_request */
                foreach ($pending_sms_requests as $phone_verification_request) {
                    $this->sendSmsVerificationLink($phone_verification_request);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return CommandAlias::SUCCESS;
    }

    protected function sendEmailVerificationLink(ConsumerEmailVerificationRequest $verification_request): void
    {
        if (!empty($verification_request->email_verification_link_sent_at)) {
            return;
        }

        if (!empty($verification_request->email_verified_at)) {
            return;
        }

        $verification_request->sendEmailVerificationNotification();

        $verification_request->email_verification_link_sent_at = now();
        $verification_request->save();

        $consumer = $verification_request->consumer;
        /** @var Consumer $consumer */
        $consumer->sendings()->create([
            'type' => 'email',
            'subject' => __('Verify Email Address'),
        ]);
    }

    protected function sendSmsVerificationLink(ConsumerPhoneVerificationRequest $verification_request)
    {
        if (!empty($verification_request->sms_verification_link_sent_at)) {
            return;
        }

        if (!empty($verification_request->phone_verified_at)) {
            return;
        }

        if (empty($verification_request->expires_at)) {
            $verification_request->expires_at = now()->addMinutes(15);
            $verification_request->save();
        }

        $local_url = route('consumer-sms-verification', [
            'sms_verification_hash' => $verification_request->sms_verification_hash,
        ]);

        $local_url = str_replace('https://www.', 'https://', $local_url);

        $sms_message = new VerificationLinkMessage($local_url);

        $this->sms_sender->sendSms(
            country_code: config('app.country_code'),
            phone: $verification_request->phone,
            alpha_name: $sms_message->sender, // This must be allowed by SMS Gateway (Sales.lv)
            message: $sms_message->message,
        );

        $verification_request->sms_verification_link_sent_at = now();
        $verification_request->save();

        $consumer = $verification_request->consumer;
        /** @var Consumer $consumer */
        $consumer->sendings()->create([
            'type' => 'sms',
            'subject' => $sms_message->sender,
            'message' => Str::length($sms_message->message) > 191 ? Str::substr($sms_message->message, 0, 191) : $sms_message->message,
        ]);
    }
}
