<?php

use Illuminate\Support\Str;
use Tompec\EmailLog\Models\EmailEvent;

$factory->define(EmailEvent::class, function () {
    return [
        'name' => 'test',
        'provider' => 'foo',
        'provider_event_id' => Str::random(10),
        'name' => 'test',
        'email_log_id' => 1,
        'data' => ['foo' => 'bar'],
    ];
});
