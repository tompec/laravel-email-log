<?php

namespace Tompec\EmailLog\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        $response = (new Client)->get('https://api.mailgun.net/v3/'.config('services.mailgun.domain').'/events', [
            'auth' => [
                'api',
                config('services.mailgun.secret'),
            ],
            'query' => [
                'message-id' => $this->email->provider_email_id,
            ],
        ])->getBody()->getContents();

        $json = json_decode($response, true);

        foreach ($json['items'] as $event) {
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
