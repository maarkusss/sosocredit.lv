<?php

namespace App\Contracts;

use App\Models\Consumer;
use App\Models\Consumer\ConsumerDevice;

interface HoiFmServiceInterface
{
    public function generateNewToken(): string;

    public function addPhoneNumber($name, $first_name, $last_name, $personal_code, string $phone_number, string $hoi_token, array $user_agent, $fingerprint);

    public function removePhoneNumber(string $phone_number);

    public function unsubscribePhoneNumber(string $phone_number);

    public function unsubscribePhoneNumberFromProjectPrefix(string $phone_number, string $project_prefix);

    public function updateHoiTokenForPhoneNumber(string $phone_number, string $new_hoi_token);

    public function addEmail($name, string $email, string $hoi_token, array $user_agent, $fingerprint);

    public function removeEmail(string $email);

    public function unsubscribeEmail(string $email);

    public function unsubscribeEmailFromProjectPrefix(string $email, string $project_prefix);

    public function reInsertConsumerEmail(Consumer $consumer);

    public function reInsertConsumerPhone(Consumer $consumer);

    public function shouldExportEmailToHoi(Consumer $consumer): bool;

    public function shouldExportPhoneToHoi(Consumer $consumer): bool;

    public function unsubscribeConsumer(Consumer $consumer);

    public function addPushConsumer(Consumer $consumer);

    public function addPushDevice(ConsumerDevice $device): int;

    public function updatePushDevice(ConsumerDevice $device): int;

    public function upsertPushDevice(ConsumerDevice $device): int;

    public function deletePushDevice(ConsumerDevice $device): int;
    
    public function updatePushConsumer(Consumer $consumer);

    public function upsertPushConsumer(Consumer $consumer);

    public function deletePushConsumer(Consumer $consumer);

    public function unsubscribePushConsumer(Consumer $consumer);

    public function resetPushDevice(ConsumerDevice $device, string $push_notification_url = ''): int;

    public function shortenUrl(string $full_url): string;

    public function senderName(): string;

    public function senderDomain(): string;

    public function fromHeader(): string;
}
