<?php

use GlennRaya\Xendivel\Http\Middleware\VerifyWebhookSignature;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

beforeEach(function () {
    config([
        'xendivel.verify_webhook_signature' => true,
        'xendivel.webhook_verification_token' => 'testing-webhook-token',
    ]);
});

it('rejects webhook requests with missing or invalid callback tokens', function (?string $token) {
    $request = Request::create('/xendit/webhook', 'POST', server: array_filter([
        'HTTP_X_CALLBACK_TOKEN' => $token,
    ]));

    expect(fn () => (new VerifyWebhookSignature)->handle(
        $request,
        fn () => response('accepted')
    ))->toThrow(AccessDeniedHttpException::class, 'Access denied');
})->with([
    'missing token' => [null],
    'invalid token' => ['wrong-token'],
]);

it('allows webhook requests with a valid callback token', function () {
    $request = Request::create('/xendit/webhook', 'POST', server: [
        'HTTP_X_CALLBACK_TOKEN' => 'testing-webhook-token',
    ]);

    $response = (new VerifyWebhookSignature)->handle(
        $request,
        fn () => response('accepted', 202)
    );

    expect($response->getStatusCode())->toBe(202)
        ->and($response->getContent())->toBe('accepted');
});

it('guards the qr code webhook path with the same callback token check', function () {
    $request = Request::create(config('xendivel.qr_webhook_url'), 'POST', server: [
        'HTTP_X_CALLBACK_TOKEN' => 'wrong-token',
    ]);

    expect(fn () => (new VerifyWebhookSignature)->handle(
        $request,
        fn () => response('accepted')
    ))->toThrow(AccessDeniedHttpException::class, 'Access denied');
});

it('allows unsigned webhook requests when verification is disabled', function () {
    config(['xendivel.verify_webhook_signature' => false]);

    $request = Request::create('/xendit/webhook', 'POST');

    $response = (new VerifyWebhookSignature)->handle(
        $request,
        fn () => response('accepted', 202)
    );

    expect($response->getStatusCode())->toBe(202);
});
