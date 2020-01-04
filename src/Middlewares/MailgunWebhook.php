<?php

namespace Tompec\EmailLog\Middlewares;

use Closure;
use Illuminate\Http\Response;

class MailgunWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->verify($request)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    public function verify($request)
    {
        $token = $request->input('signature.token');
        $timestamp = $request->input('signature.timestamp');
        $signature = $request->input('signature.signature');

        // check if the timestamp is fresh
        if (abs(time() - $timestamp) > 15) {
            return false;
        }

        // returns true if signature is valid
        return hash_hmac('sha256', $timestamp.$token, config('services.mailgun.secret')) === $signature;
    }
}
