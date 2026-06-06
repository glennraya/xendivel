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
