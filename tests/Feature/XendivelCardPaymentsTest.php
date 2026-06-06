<?php

use GlennRaya\Xendivel\Xendivel;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

beforeEach(function () {
    config([
        'xendivel.auto_id' => false,
        'xendivel.redirects.failure_url' => null,
        'xendivel.redirects.success_url' => null,
        'xendivel.secret_key' => 'sk_test_123',
    ]);
});

it('charges cards with the required payload and non-empty optional fields', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'card-charge-123',
            'status' => 'CAPTURED',
        ]),
    ]);

    $payment = Xendivel::payWithCard(xendivelCardPaymentRequest([
        'descriptor' => 'Xendivel Store',
        'currency' => '',
        'metadata' => ['order_id' => 'order-123'],
    ]));

    expect($payment)->toBeInstanceOf(Xendivel::class)
        ->and($payment->getResponse()->id)->toBe('card-charge-123');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges')
            && $request['amount'] === 2500
            && $request['external_id'] === 'manual-external-id'
            && $request['token_id'] === 'token-123'
            && $request['authentication_id'] === 'auth-123'
            && $request['descriptor'] === 'Xendivel Store'
            && $request['metadata'] === ['order_id' => 'order-123']
            && ! array_key_exists('currency', $request->data());
    });

    Http::assertSentCount(1);
});

it('generates a card external id when auto id is enabled', function () {
    config(['xendivel.auto_id' => true]);

    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'card-charge-456',
            'status' => 'CAPTURED',
        ]),
    ]);

    Xendivel::payWithCard(xendivelCardPaymentRequest([
        'external_id' => 'ignored-external-id',
    ]));

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && $request['external_id'] !== 'ignored-external-id'
            && Str::isUuid((string) $request['external_id']);
    });
});

it('does not send card payment requests when validation fails', function () {
    Http::fake();

    expect(fn () => Xendivel::payWithCard(xendivelCardPaymentRequest([
        'amount' => 19,
    ])))->toThrow(Exception::class, 'The amount must be at least 20.');

    Http::assertNothingSent();
});

it('authorizes cards without capturing immediately', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'card-auth-123',
            'status' => 'AUTHORIZED',
        ]),
    ]);

    $payment = Xendivel::authorizeCard(xendivelCardPaymentRequest([
        'descriptor' => 'Xendivel Store',
        'currency' => '',
        'metadata' => ['order_id' => 'order-123'],
    ]));

    expect($payment)->toBeInstanceOf(Xendivel::class)
        ->and($payment->getResponse()->status)->toBe('AUTHORIZED');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges')
            && $request['amount'] === 2500
            && $request['external_id'] === 'manual-external-id'
            && $request['token_id'] === 'token-123'
            && $request['authentication_id'] === 'auth-123'
            && $request['descriptor'] === 'Xendivel Store'
            && $request['metadata'] === ['order_id' => 'order-123']
            && $request['capture'] === false
            && ! array_key_exists('currency', $request->data());
    });
});

it('generates a card external id for authorization when auto id is enabled', function () {
    config(['xendivel.auto_id' => true]);

    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'card-auth-456',
            'status' => 'AUTHORIZED',
        ]),
    ]);

    Xendivel::authorizeCard(xendivelCardPaymentRequest([
        'external_id' => 'ignored-external-id',
    ]));

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && $request['capture'] === false
            && $request['external_id'] !== 'ignored-external-id'
            && Str::isUuid((string) $request['external_id']);
    });
});

it('refunds card payments through the card refund endpoint', function () {
    Http::fake(function (ClientRequest $request) {
        if ($request->method() === 'GET') {
            return Http::response([
                'id' => 'card-charge-123',
                'status' => 'CAPTURED',
            ]);
        }

        return Http::response([
            'id' => 'card-refund-123',
            'status' => 'SUCCEEDED',
        ]);
    });

    $payment = Xendivel::getPayment('card-charge-123', 'card')
        ->refund(500, 'refund-external-id');

    expect($payment->refund_response->json('id'))->toBe('card-refund-123');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges/card-charge-123/refunds')
            && $request['amount'] === 500
            && $request['external_id'] === 'refund-external-id'
            && str_ends_with((string) $request['idempotency'], 'x-idempotency-key')
            && $request->hasHeader('X-IDEMPOTENCY-KEY', $request['idempotency']);
    });

    Http::assertSentCount(2);
});

it('captures an authorized card charge with the provided amount', function () {
    Http::fake(function (ClientRequest $request) {
        if ($request->method() === 'GET') {
            return Http::response([
                'id' => 'card-charge-123',
                'status' => 'AUTHORIZED',
            ]);
        }

        return Http::response([
            'id' => 'card-charge-123',
            'status' => 'CAPTURED',
            'capture_amount' => 2000,
        ]);
    });

    $payment = Xendivel::getPayment('card-charge-123', 'card')
        ->captureCardCharge(2000);

    expect($payment->getResponse()->capture_amount)->toBe(2000);

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges/card-charge-123/capture')
            && $request['amount'] === 2000;
    });

    Http::assertSentCount(2);
});

it('voids a card authorization with a custom external id', function () {
    Http::fake(function (ClientRequest $request) {
        if ($request->method() === 'GET') {
            return Http::response([
                'id' => 'card-charge-123',
                'status' => 'AUTHORIZED',
            ]);
        }

        return Http::response([
            'id' => 'card-charge-123',
            'status' => 'REVERSED',
        ]);
    });

    $payment = Xendivel::getPayment('card-charge-123', 'card')
        ->voidCardAuthorization('void-external-id');

    expect($payment->getResponse()->status)->toBe('REVERSED');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges/card-charge-123/auth_reversal')
            && $request['external_id'] === 'void-external-id';
    });

    Http::assertSentCount(2);
});

it('generates a reversal external id when auto id is enabled', function () {
    config(['xendivel.auto_id' => true]);

    Http::fake(function (ClientRequest $request) {
        if ($request->method() === 'GET') {
            return Http::response([
                'id' => 'card-charge-123',
                'status' => 'AUTHORIZED',
            ]);
        }

        return Http::response([
            'id' => 'card-charge-123',
            'status' => 'REVERSED',
        ]);
    });

    Xendivel::getPayment('card-charge-123', 'card')
        ->voidCardAuthorization();

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges/card-charge-123/auth_reversal')
            && Str::isUuid((string) $request['external_id']);
    });
});

it('requires a custom reversal external id when auto id is disabled', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'card-charge-123',
            'status' => 'AUTHORIZED',
        ]),
    ]);

    expect(fn () => Xendivel::getPayment('card-charge-123', 'card')
        ->voidCardAuthorization())->toThrow(Exception::class, 'external_id');

    Http::assertSentCount(1);
});

it('prevents card authorization actions from running on non-card payments', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'ewallet-charge-123',
            'status' => 'SUCCEEDED',
        ]),
    ]);

    expect(fn () => Xendivel::getPayment('ewallet-charge-123', 'ewallet')
        ->captureCardCharge(1000))->toThrow(Exception::class, 'Card charge context is required')
        ->and(fn () => Xendivel::getPayment('ewallet-charge-123', 'ewallet')
            ->voidCardAuthorization('void-external-id'))->toThrow(Exception::class, 'Card charge context is required');

    Http::assertSentCount(2);
});

function xendivelCardPaymentRequest(array $overrides = []): LaravelRequest
{
    return LaravelRequest::create('/pay-with-card', 'POST', array_merge([
        'amount' => 2500,
        'external_id' => 'manual-external-id',
        'token_id' => 'token-123',
        'authentication_id' => 'auth-123',
    ], $overrides));
}
