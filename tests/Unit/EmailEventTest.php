<?php

namespace Tompec\EmailLog\Tests\Unit;

use Tompec\EmailLog\Models\EmailEvent;
use Tompec\EmailLog\Models\EmailLog;
use Tompec\EmailLog\Tests\TestCase;

class EmailEventTest extends TestCase
{
    /** @test */
    public function it_has_the_needed_properties()
    {
        $emailEvent = factory(EmailEvent::class)->create([
            'provider' => 'foo',
            'provider_event_id' => 'bar',
            'name' => 'opened',
            'data' => ['foo' => 'bar'],
        ]);

        $this->assertEquals('foo', $emailEvent->provider);
        $this->assertEquals('bar', $emailEvent->provider_event_id);
        $this->assertEquals('opened', $emailEvent->name);
        $this->assertIsArray($emailEvent->data);
        $this->assertEquals('bar', $emailEvent->data['foo']);
    }

    /** @test **/
    public function it_belongs_to_a_email_log()
    {
        $emailLog = factory(EmailLog::class)->create();

        $emailEvent = factory(EmailEvent::class)->create([
            'email_log_id' => $emailLog->id,
        ]);

        $this->assertInstanceOf(EmailLog::class, $emailEvent->email);
        $this->assertEquals($emailLog->id, $emailEvent->email->id);
    }
}
