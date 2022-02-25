<?php

namespace Tompec\EmailLog\Events;

use Illuminate\Mail\Events\MessageSending;
use Tompec\EmailLog\Models\EmailLog;

class NewEmailSent
{
    /**
     * Handle the event.
     *
     * @param  MessageSending  $event
     */
    public function handle(MessageSending $event)
    {
        $message = $event->message;
        $headers = $message->getHeaders();

        $recipientModel = config('email-log.recipient_model');

        $recipientEmail = $this->getTo($headers);

        $recipient = $recipientModel::where(config('email-log.recipient_email_column'), $recipientEmail)->first();

        if ($recipient || config('email-log.log_unknown_recipients')) {
            EmailLog::create([
                'from' => $this->getFrom($headers),
                'to' => $recipientEmail,
                'subject' => $message->getSubject(),
                'body' => $message->getBody()->toString(),

                'provider' => config('mail.default'),
                'provider_email_id' => $message->generateMessageId(),

                'recipient_type' => $recipient ? config('email-log.recipient_model') : null,
                'recipient_id' => optional($recipient)->id,
            ]);
        }
    }

    public function getFrom($headers)
    {
        return collect($headers->get('From')->getAddressStrings())->first();
    }

    public function getTo($headers)
    {
        return collect($headers->get('To')->getAddressStrings())->first();
    }
}
