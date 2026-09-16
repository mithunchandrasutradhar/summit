<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Safe no-op default: logs what would have been sent instead of actually
 * sending it. Swap the `SmsGateway` binding in AppServiceProvider for a
 * real driver (e.g. SSL Wireless, Alpha SMS) once BACCO selects a provider.
 */
class LogSmsDriver implements SmsGateway
{
    public function send(string $toPhoneNumber, string $message): bool
    {
        Log::info('[SMS] would send', ['to' => $toPhoneNumber, 'message' => $message]);

        return true;
    }
}
