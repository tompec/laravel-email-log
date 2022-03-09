<?php

namespace Tompec\EmailLog\Events;

use Illuminate\Mail\Events\MessageSent;
use Tompec\EmailLog\Models\EmailLog;

class NewEmailSent
{
    /**
     * Handle the event.
     *
     * @param  MessageSent  $event
     */
    public function handle(MessageSent $event)
    {
        $recipientModel = config('email-log.recipient_model');

        $recipientEmail = $event->message->getTo()[0]->getAddress();

        $recipient = $recipientModel::where(config('email-log.recipient_email_column'), $recipientEmail)->first();

        if ($recipient || config('email-log.log_unknown_recipients')) {
            EmailLog::create([
                'from' => $event->message->getFrom()[0]->toString(),
                'to' => $recipientEmail,
                'subject' => $event->message->getSubject(),
                'body' => $event->message->getHtmlBody(),

                'provider' => config('mail.default'),
                'provider_email_id' => str($event->sent->getMessageId())->trim('<>')->__toString(),

                'recipient_type' => $recipient ? config('email-log.recipient_model') : null,
                'recipient_id' => $recipient?->id,
            ]);
        }
    }
}
