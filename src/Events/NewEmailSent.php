<?php

namespace Tompec\EmailLog\Events;

use Illuminate\Mail\Events\MessageSending;
use Tompec\EmailLog\Models\EmailLog;

class NewEmailSent
{
    /**
     * Handle the event.
     *
     * @param MessageSending $event
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
                'body' => $message->getBody(),

                'provider' => config('mail.default'),
                'provider_email_id' => $message->getId(),

                'recipient_type' => $recipient ? config('email-log.recipient_model') : null,
                'recipient_id' => optional($recipient)->id,
            ]);
        }
    }

    public function getFrom($headers)
    {
        return collect($headers->get('From')->getFieldBodyModel())
                    ->map(function ($name, $email) {
                        return "{$name} <{$email}>";
                    })
                    ->first();
    }

    public function getTo($headers)
    {
        return $headers->has('To') ? collect($headers->get('To')->getFieldBodyModel())->keys()->first() : null;
    }
}
