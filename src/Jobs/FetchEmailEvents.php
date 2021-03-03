<?php

namespace Tompec\EmailLog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Tompec\EmailLog\Models\EmailEvent;
use Tompec\EmailLog\Models\EmailLog;

class FetchEmailEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(EmailLog $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::withBasicAuth('api', config('services.mailgun.secret'))->
                            get('https://api.mailgun.net/v3/'.config('services.mailgun.domain').'/events', [
                                'message-id' => $this->email->provider_email_id,
                            ])
                            ->throw()
                            ->json();

        foreach ($response['items'] as $event) {
            EmailEvent::firstOrCreate([
                'provider_event_id' => $event['id'],
            ], [
                'email_log_id' => $this->email->id,
                'provider' => 'mailgun',
                'name' => $event['event'],
                'data' => $event,
                'created_at' => $event['timestamp'],
            ]);
        }
    }
}
