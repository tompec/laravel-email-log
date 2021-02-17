<?php

namespace Tompec\EmailLog\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tompec\EmailLog\Jobs\FetchEmailEvents;
use Tompec\EmailLog\Models\EmailEvent;
use Tompec\EmailLog\Models\EmailLog;

class SaveLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message_id = $this->data['message']['headers']['message-id'];

        $email_log = EmailLog::where('provider', 'mailgun')->where('provider_email_id', $message_id)->first();

        if (! $email_log) {
            // If Mailgun receives a 406 (Not Acceptable) code, Mailgun will determine the POST is rejected and not retry.
            return response()->json(['No email log found'], 406);
        }

        if (in_array($this->data['event'], ['opened', 'clicked', 'delivered', 'failed'])) {
            if ($email_log->{$this->data['event'].'_at'} == null) {
                $email_log->update(["{$this->data['event']}_at" => now()]);
            }
            if (config('email-log.log_events')) {
                FetchEmailEvents::dispatch($email_log)->onQueue(config('email-log.queue'));
            }
        }
    }
}
