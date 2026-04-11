<?php

use GlennRaya\Xendivel\XenditApi;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['xendivel.secret_key' => '']);
});

it('requires a Xendit secret key before sending requests', function () {
    Http::fake();

    expect(fn () => XenditApi::api('post', '/credit_card_charges', [
        'amount' => 2500,
    ]))->toThrow(Exception::class, 'XENDIT_SECRET_KEY');

    Http::assertNothingSent();
});

it('sends authenticated requests with the configured API headers', function () {
    config(['xendivel.secret_key' => 'sk_test_123']);

    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'card-charge-123',
            'status' => 'CAPTURED',
        ]),
    ]);

    $response = XenditApi::api('post', '/credit_card_charges', [
        'amount' => 2500,
        'idempotency' => 'idem-key-123',
    ]);

    expect($response->json('id'))->toBe('card-charge-123')
        ->and((new XenditApi)->getResponse()->id)->toBe('card-charge-123');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges')
            && $request['amount'] === 2500
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('sk_test_123:'))
            && $request->hasHeader('x-api-version', '2019-05-01')
            && $request->hasHeader('X-IDEMPOTENCY-KEY', 'idem-key-123');
    });

    Http::assertSentCount(1);
});

it('throws when Xendit returns a failed response', function () {
    config(['xendivel.secret_key' => 'sk_test_123']);

    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'message' => 'Invalid request',
        ], 422),
    ]);

    expect(fn () => XenditApi::api('post', '/credit_card_charges', [
        'amount' => 2500,
    ]))->toThrow(Exception::class);

    Http::assertSentCount(1);
});
