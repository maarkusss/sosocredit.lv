<?php

namespace App\Services\Sms\Messages;

abstract class Message
{
    public string $sender;
    public string $message;
}
