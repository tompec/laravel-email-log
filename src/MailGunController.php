<?php

namespace Tompec\EmailLog;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tompec\EmailLog\Jobs\SaveLog;
use Tompec\EmailLog\Middlewares\MailgunWebhook;

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

        SaveLog::dispatch($data)->onQueue(config('email-log.queue'));
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
