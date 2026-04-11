<?php

use GlennRaya\Xendivel\Services\OtcService;
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

it('charges e-wallets with a manual reference id', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'ewallet-charge-123',
            'status' => 'PENDING',
        ]),
    ]);

    Xendivel::payWithEwallet(xendivelEwalletPaymentRequest());

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/ewallets/charges')
            && $request['reference_id'] === 'manual-reference-id'
            && $request['currency'] === 'PHP'
            && $request['amount'] === 2500
            && $request['checkout_method'] === 'ONE_TIME_PAYMENT'
            && $request['channel_code'] === 'PH_GCASH';
    });
});

it('fills missing e-wallet redirect urls with package return routes', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'ewallet-charge-123',
            'status' => 'PENDING',
        ]),
    ]);

    Xendivel::payWithEwallet(xendivelEwalletPaymentRequest([
        'channel_properties' => [],
    ]));

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/ewallets/charges')
            && $request['channel_properties']['success_redirect_url'] === route('xendivel.payment.success')
            && $request['channel_properties']['failure_redirect_url'] === route('xendivel.payment.failed');
    });
});

it('preserves custom e-wallet redirect urls', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'ewallet-charge-123',
            'status' => 'PENDING',
        ]),
    ]);

    $channel_properties = [
        'success_redirect_url' => 'https://merchant.test/payment/success',
        'failure_redirect_url' => 'https://merchant.test/payment/failed',
    ];

    Xendivel::payWithEwallet(xendivelEwalletPaymentRequest([
        'channel_properties' => $channel_properties,
    ]));

    Http::assertSent(function (ClientRequest $request) use ($channel_properties) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/ewallets/charges')
            && $request['channel_properties'] === $channel_properties;
    });
});

it('uses configured e-wallet redirect urls before package return routes', function () {
    config([
        'xendivel.redirects.success_url' => 'https://merchant.test/checkout/thanks',
        'xendivel.redirects.failure_url' => 'https://merchant.test/checkout/retry',
    ]);

    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'ewallet-charge-123',
            'status' => 'PENDING',
        ]),
    ]);

    Xendivel::payWithEwallet(xendivelEwalletPaymentRequest([
        'channel_properties' => [],
    ]));

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/ewallets/charges')
            && $request['channel_properties']['success_redirect_url'] === 'https://merchant.test/checkout/thanks'
            && $request['channel_properties']['failure_redirect_url'] === 'https://merchant.test/checkout/retry';
    });
});

it('generates an e-wallet reference id when auto id is enabled', function () {
    config(['xendivel.auto_id' => true]);

    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'ewallet-charge-456',
            'status' => 'PENDING',
        ]),
    ]);

    Xendivel::payWithEwallet(xendivelEwalletPaymentRequest([
        'reference_id' => 'ignored-reference-id',
    ]));

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && $request['reference_id'] !== 'ignored-reference-id'
            && Str::isUuid((string) $request['reference_id']);
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

it('refunds e-wallet payments with an uppercased reason', function () {
    Http::fake(function (ClientRequest $request) {
        if ($request->method() === 'GET') {
            return Http::response([
                'id' => 'ewallet-charge-123',
                'status' => 'SUCCEEDED',
            ]);
        }

        return Http::response([
            'id' => 'ewallet-refund-123',
            'status' => 'SUCCEEDED',
        ]);
    });

    $payment = Xendivel::getPayment('ewallet-charge-123', 'ewallet')
        ->refund(500, reason: 'requested_by_customer');

    expect($payment->refund_response->json('id'))->toBe('ewallet-refund-123');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/ewallets/charges/ewallet-charge-123/refunds')
            && $request['amount'] === 500
            && $request['reason'] === 'REQUESTED_BY_CUSTOMER';
    });

    Http::assertSentCount(2);
});

it('voids e-wallet charges', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'ewallet-charge-123',
            'status' => 'VOIDED',
        ]),
    ]);

    Xendivel::void('ewallet-charge-123');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/ewallets/charges/ewallet-charge-123/void')
            && $request->data() === [];
    });
});

it('creates and simulates OTC payment codes', function (string $method, string $endpoint, array $payload) {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'status' => 'SUCCEEDED',
        ]),
    ]);

    $service = Xendivel::otc();

    expect($service)->toBeInstanceOf(OtcService::class);

    $service->{$method}($payload);

    Http::assertSent(function (ClientRequest $request) use ($endpoint, $payload) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), $endpoint)
            && $request->data() === $payload;
    });
})->with([
    'create payment code' => [
        'createPaymentCode',
        '/payment_codes',
        [
            'reference_id' => 'otc-reference-id',
            'channel_code' => 'CEBUANA',
            'customer_name' => 'Glenn Raya',
            'amount' => 340,
            'currency' => 'PHP',
            'market' => 'PH',
        ],
    ],
    'simulate payment' => [
        'simulateOtcPayment',
        '/payment_codes/simulate_payment',
        [
            'reference_id' => 'otc-reference-id',
            'payment_code' => 'JSNFAKYYDJ4544',
            'channel_code' => 'CEBUANA',
            'amount' => 340,
            'currency' => 'PHP',
            'market' => 'PH',
        ],
    ],
]);

function xendivelCardPaymentRequest(array $overrides = []): LaravelRequest
{
    return LaravelRequest::create('/pay-with-card', 'POST', array_merge([
        'amount' => 2500,
        'external_id' => 'manual-external-id',
        'token_id' => 'token-123',
        'authentication_id' => 'auth-123',
    ], $overrides));
}

function xendivelEwalletPaymentRequest(array $overrides = []): LaravelRequest
{
    return LaravelRequest::create('/pay-via-ewallet', 'POST', array_merge([
        'reference_id' => 'manual-reference-id',
        'currency' => 'PHP',
        'amount' => 2500,
        'checkout_method' => 'ONE_TIME_PAYMENT',
        'channel_code' => 'PH_GCASH',
        'channel_properties' => [
            'success_redirect_url' => 'https://example.com/success',
        ],
    ], $overrides));
}
