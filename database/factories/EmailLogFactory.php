<?php

use Tompec\EmailLog\Models\EmailLog;

$factory->define(EmailLog::class, function () {
    return [
        'from' => 'from@example.com',
        'to' => 'to@example.com',
        'subject' => 'Test subject',
        'body' => 'Test body',
        'provider' => 'log',
        'provider_email_id' => '1234',
        'recipient_id' => 1,
        'recipient_type' => 'App\User',
    ];
});
