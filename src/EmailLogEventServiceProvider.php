<?php

namespace Tompec\EmailLog;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class EmailLogEventServiceProvider extends EventServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MessageSending::class => [
            EmailLogEvent::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
