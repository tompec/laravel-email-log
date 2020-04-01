<?php

namespace Tompec\EmailLog\Tests\Feature;

use Tompec\EmailLog\EmailLog;
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

        $response->assertStatus(406);
    }

    /** @test **/
    public function a_webhook_with_no_message_id_returns_a_406()
    {
        $response = $this->makeInvalidRequest(config('services.mailgun.secret'));

        $response->assertStatus(406);
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
