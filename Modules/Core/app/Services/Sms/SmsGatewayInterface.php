<?php

namespace Modules\Core\Services\Sms;

interface SmsGatewayInterface
{
    /**
     * Send a text message to a full E.164-ish phone number (country code + number already combined).
     */
    public function send(string $phoneCode, string $phoneNumber, string $message): bool;
}
