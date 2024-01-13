<?php

namespace GlennRaya\Xendivel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VerifyWebhookSignature
{
    /**
     * Verify the webhook callback signature if it's legitimately from Xendit.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('xendivel.verify_webhook_signature') === true) {
            if ($request->header('x-callback-token') !== config('xendivel.webhook_verification_token')) {
                logger('403 Access denied: Webhook verification token is invalid or non-existent. This request might come from illegitimate services.');

                throw new AccessDeniedHttpException('Access denied: Webhook verification token is invalid or non-existent. This request might come from illegitimate services.');
            }
        }

        return $next($request);
    }
}
