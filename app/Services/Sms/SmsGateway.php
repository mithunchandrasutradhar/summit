<?php

namespace App\Services\Sms;

interface SmsGateway
{
    /**
     * Send an SMS message. Returns true on success.
     */
    public function send(string $toPhoneNumber, string $message): bool;
}
