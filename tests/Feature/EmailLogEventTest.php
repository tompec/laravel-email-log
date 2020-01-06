<?php

namespace Tompec\EmailLog\Tests\Feature;

use Tompec\EmailLog\Tests\User;
use Tompec\EmailLog\Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Tompec\EmailLog\Tests\TestEmail;

class EmailLogEventTest extends TestCase
{
    /** @test */
    public function an_email_is_logged_when_it_is_sent()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'email' => 'test@example.com',
        ]);

        Mail::to('test@example.com')->send(new TestEmail);

        $this->assertDatabaseHas('email_log', [
            'from' => 'Example <hello@example.com>',
            'to' => 'test@example.com',
            'subject' => 'Test subject',
            'body' => "test body\n",
            'provider' => 'array',
            'recipient_id' => $user->id,
            'recipient_type' => config('email-log.recipient_model'),
        ]);
    }

    /** @test */
    public function an_email_to_an_unknown_user_is_logged_when_it_is_sent()
    {
        $this->withoutExceptionHandling();

        Mail::to('unknown@example.com')->send(new TestEmail);

        $this->assertDatabaseHas('email_log', [
            'from' => 'Example <hello@example.com>',
            'to' => 'unknown@example.com',
            'subject' => 'Test subject',
            'body' => "test body\n",
            'provider' => 'array',
            'recipient_id' => null,
            'recipient_type' => null,
        ]);
    }
}
