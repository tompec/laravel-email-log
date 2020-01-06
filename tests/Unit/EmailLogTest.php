<?php

namespace Tompec\EmailLog\Tests\Unit;

use Carbon\Carbon;
use Tompec\EmailLog\EmailLog;
use Tompec\EmailLog\Tests\User;
use Tompec\EmailLog\Tests\TestCase;

class EmailLogTest extends TestCase
{
    /** @test */
    public function it_has_the_needed_properties()
    {
        $emailLog = factory(EmailLog::class)->create([
            'from' => 'from',
            'to' => 'to',
            'subject' => 'subject',
            'body' => 'body',
            'provider' => 'pigeon',
            'provider_email_id' => '1234',
            'recipient_id' => 1,
            'recipient_type' => 'App\User',
        ]);

        $this->assertEquals('from', $emailLog->from);
        $this->assertEquals('to', $emailLog->to);
        $this->assertEquals('subject', $emailLog->subject);
        $this->assertEquals('body', $emailLog->body);
        $this->assertEquals('pigeon', $emailLog->provider);
        $this->assertEquals('1234', $emailLog->provider_email_id);
        $this->assertEquals(1, $emailLog->recipient_id);
        $this->assertEquals('App\User', $emailLog->recipient_type);

        $this->assertNull($emailLog->delivered_at);
        $this->assertNull($emailLog->failed_at);
        $this->assertNull($emailLog->opened_at);
        $this->assertNull($emailLog->clicked_at);

        $this->assertInstanceOf(Carbon::class, $emailLog->created_at);
        $this->assertInstanceOf(Carbon::class, $emailLog->updated_at);
    }

    /** @test **/
    public function an_email_log_has_a_recipient()
    {
        $user = factory(User::class)->create([
            'email' => 'user@example.com',
        ]);

        $emailLog = factory(EmailLog::class)->create([
            'recipient_id' => $user->id,
            'recipient_type' => config('email-log.recipient_model'),
        ]);

        $this->assertInstanceOf(config('email-log.recipient_model'), $emailLog->recipient);
        $this->assertEquals('user@example.com', $emailLog->recipient->email);
    }
}
