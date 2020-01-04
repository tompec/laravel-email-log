<?php

namespace Tompec\EmailLog;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

        $message_id = $data['message']['headers']['message-id'];

        $delivery = EmailLog::where('provider', 'mailgun')->where('provider_email_id', $message_id)->first();

        if (! $delivery) {
            // If Mailgun receives a 406 (Not Acceptable) code, Mailgun will determine the POST is rejected and not retry.
            abort(406);
        }

        if (in_array($data['event'], ['opened', 'clicked', 'delivered', 'failed'])) {
            if ($delivery->{$data['event'] . '_at'} == null) {
                $delivery->update(["{$data['event']}_at" => now()]);
            }
        }
    }
}
