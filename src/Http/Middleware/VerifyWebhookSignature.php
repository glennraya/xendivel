<?php

namespace GlennRaya\Xendivel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if($request->header('x-callback-token') === config('xendivel.webhook_verification_token')) return $next($request);

        if($request->header('x-callback-token') !== config('xendivel.webhook_verification_token')) {
            throw new AccessDeniedHttpException('Access denied: Webhook verification signature is invalid or non-existent.');
        }

        return $next($request);

    }
}
