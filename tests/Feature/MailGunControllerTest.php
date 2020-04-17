<?php

namespace Tompec\EmailLog\Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Tompec\EmailLog\Jobs\FetchEmailEvents;
use Tompec\EmailLog\Models\EmailLog;
use Tompec\EmailLog\Tests\TestCase;

class MailGunControllerTest extends TestCase
{
    /** @test */
    public function an_email_log_is_updated_when_a_webhook_is_received()
    {
        $this->withoutExceptionHandling();

        $email = factory(EmailLog::class)->create([
            'provider' => 'mailgun',
            'provider_email_id' => 'email_id',
        ]);

        foreach (['delivered', 'failed', 'opened', 'clicked'] as $status) {
            $this->assertNull($email->{$status.'_at'});

            $response = $this->makeRequest($status, 'email_id', config('services.mailgun.secret'));

            $response->assertStatus(200);

            $this->assertNotNull($email->fresh()->{$status.'_at'});
        }
    }

    /** @test **/
    public function a_webhook_too_old_is_not_valid()
    {
        $response = $this->makeRequest('delivered', 'email_id', config('services.mailgun.secret'), now()->subSeconds(16)->getTimestamp());

        $response->assertStatus(403);
    }

    /** @test **/
    public function a_webhook_with_an_invalid_signature_returns_a_403()
    {
        $response = $this->makeRequest('delivered', 'email_id', 'wrong_secret');

        $response->assertStatus(403);
    }

    /** @test **/
    public function a_get_request_returns_a_405()
    {
        $response = $this->json('GET', '/email-log/mailgun');

        $response->assertStatus(405);
    }

    /** @test **/
    public function a_webhook_with_for_an_email_not_found_returns_a_406()
    {
        $response = $this->makeRequest('delivered', 'wrong_email_id', config('services.mailgun.secret'));

        $response->assertSee('No email log found')->assertStatus(406);
    }

    /** @test **/
    public function a_webhook_with_no_message_id_returns_a_406()
    {
        $response = $this->makeInvalidRequest(config('services.mailgun.secret'));

        $response->assertSee('No message-id found')->assertStatus(406);
    }

    /** @test **/
    public function the_fetch_jobs_is_queued_when_the_config_allows_it()
    {
        Queue::fake();

        Config::set('email-log.log_events', true);

        $email = factory(EmailLog::class)->create([
            'provider' => 'mailgun',
            'provider_email_id' => 'email_id',
        ]);

        $response = $this->makeRequest('delivered', 'email_id', config('services.mailgun.secret'));

        $response->assertStatus(200);

        Queue::assertPushed(FetchEmailEvents::class, function ($job) use ($email) {
            return $job->email->id === $email->id;
        });
    }

    /** @test **/
    public function the_fetch_jobs_is_not_queued_when_the_config_disallows_it()
    {
        Queue::fake();

        Config::set('email-log.log_events', false);

        $email = factory(EmailLog::class)->create([
            'provider' => 'mailgun',
            'provider_email_id' => 'email_id',
        ]);

        $response = $this->makeRequest('delivered', 'email_id', config('services.mailgun.secret'));

        $response->assertStatus(200);

        Queue::assertNothingPushed();
    }

    /** @test **/
    public function an_event_linked_to_a_webhook_message_is_discarded()
    {
        $timestamp = time();
        $token = '';

        $response = $this->json('POST', '/email-log/mailgun', [
            'event-data' => [
                'event' => 'delivered',
                'message' => [
                    'headers' => [
                        'message-id' => 'email_id',
                    ],
                ],
                'envelope' => [
                    'targets' => route('email-log-mailgun-webhook'),
                ]
            ],
            'signature' => [
                'token' => $token,
                'timestamp' => $timestamp,
                'signature' => hash_hmac('sha256', $timestamp.$token, config('services.mailgun.secret')),
            ],
        ]);

        $response->assertSee('Webhook event')->assertStatus(200);
    }

    public function makeRequest($status, $email_id, $secret, $timestamp = null)
    {
        if (! $timestamp) {
            $timestamp = time();
        }

        $token = '';

        return $this->json('POST', '/email-log/mailgun', [
            'event-data' => [
                'event' => $status,
                'message' => [
                    'headers' => [
                        'message-id' => 'email_id',
                    ],
                ],
            ],
            'signature' => [
                'token' => $token,
                'timestamp' => $timestamp,
                'signature' => hash_hmac('sha256', $timestamp.$token, $secret),
            ],
        ]);
    }

    public function makeInvalidRequest($secret)
    {
        $timestamp = time();

        $token = '';

        return $this->json('POST', '/email-log/mailgun', [
            'event-data' => [
                'delivery-status' => [
                    'bounce-code' => '5.4.14',
                    'code' => 550,
                    'description' => '',
                    'message' => 'lorem',
                ],
                'event' => 'failed',
                'flags' => [
                    'is-delayed-bounce' => true,
                ],
                'id' => 'lorem',
                'log-level' => 'error',
                'reason' => 'bounce',
                'recipient' => 'email@example.com',
                'severity' => 'permanent',
                'timestamp' => $timestamp,
            ],
            'signature' => [
                'token' => $token,
                'timestamp' => $timestamp,
                'signature' => hash_hmac('sha256', $timestamp.$token, $secret),
            ],
        ]);
    }
}
