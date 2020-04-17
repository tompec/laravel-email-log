<?php

namespace Tompec\EmailLog;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tompec\EmailLog\Jobs\FetchEmailEvents;
use Tompec\EmailLog\Middlewares\MailgunWebhook;
use Tompec\EmailLog\Models\EmailLog;

class MailGunController extends Controller
{
    public function __construct()
    {
        $this->middleware(MailgunWebhook::class);
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->get('event-data');

        // If the event is linked to a webhook message, discard it
        if ($this->isWebhookEvent($data)) {
            return response()->json(['Webhook event'], 200);
        }

        if (! isset($data['message']['headers']['message-id'])) {
            // If Mailgun receives a 406 (Not Acceptable) code, Mailgun will determine the POST is rejected and not retry.
            return response()->json(['No message-id found'], 406);
        }

        $message_id = $data['message']['headers']['message-id'];

        $email_log = EmailLog::where('provider', 'mailgun')->where('provider_email_id', $message_id)->first();

        if (! $email_log) {
            // If Mailgun receives a 406 (Not Acceptable) code, Mailgun will determine the POST is rejected and not retry.
            return response()->json(['No email log found'], 406);
        }

        if (in_array($data['event'], ['opened', 'clicked', 'delivered', 'failed'])) {
            if ($email_log->{$data['event'].'_at'} == null) {
                $email_log->update(["{$data['event']}_at" => now()]);

                if (config('email-log.log_events')) {
                    FetchEmailEvents::dispatch($email_log)->onQueue(config('email-log.queue'));
                }
            }
        }
    }

    public function isWebhookEvent($data)
    {
        if (! isset($data['envelope']['targets'])) {
            return false;
        }

        if ($data['envelope']['targets'] !== route('email-log-mailgun-webhook')) {
            return false;
        }

        return true;
    }
}
