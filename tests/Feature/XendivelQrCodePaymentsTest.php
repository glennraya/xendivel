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

it('creates a dynamic qr payment request', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'pr-123',
            'status' => 'PENDING',
            'payment_method' => [
                'reference_id' => 'qr-ref-1',
                'qr_code' => [
                    'channel_properties' => ['qr_string' => 'qr-string-value'],
                ],
            ],
        ]),
    ]);

    $qr = Xendivel::createQrCode(xendivelQrCodeRequest());

    expect($qr)->toBeInstanceOf(Xendivel::class)
        ->and($qr->getResponse()->id)->toBe('pr-123');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/payment_requests')
            && $request['reference_id'] === 'manual-qr-external-id'
            && $request['currency'] === 'PHP'
            && $request['amount'] === 2500
            && data_get($request->data(), 'payment_method.type') === 'QR_CODE'
            && data_get($request->data(), 'payment_method.reusability') === 'ONE_TIME_USE'
            && data_get($request->data(), 'payment_method.qr_code.channel_code') === 'QRPH';
    });

    Http::assertSentCount(1);
});

it('generates a qr reference id when auto id is enabled', function () {
    config(['xendivel.auto_id' => true]);

    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'pr-456',
            'status' => 'PENDING',
        ]),
    ]);

    Xendivel::createQrCode(xendivelQrCodeRequest([
        'external_id' => 'ignored-external-id',
    ]));

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/payment_requests')
            && $request['reference_id'] !== 'ignored-external-id'
            && Str::isUuid((string) $request['reference_id']);
    });
});

it('applies a custom channel code and currency', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'pr-123',
            'status' => 'PENDING',
        ]),
    ]);

    Xendivel::createQrCode(xendivelQrCodeRequest([
        'channel_code' => 'qris',
        'currency' => 'idr',
    ]));

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/payment_requests')
            && $request['currency'] === 'IDR'
            && data_get($request->data(), 'payment_method.qr_code.channel_code') === 'QRIS';
    });
});

it('does not send dynamic qr requests without an amount', function () {
    Http::fake();

    expect(fn () => Xendivel::createQrCode(xendivelQrCodeRequest([
        'amount' => '',
    ])))->toThrow(Exception::class, 'The amount is required for DYNAMIC QR codes.');

    Http::assertNothingSent();
});

it('creates a static qr code without an amount', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'pr-static-1',
            'status' => 'PENDING',
        ]),
    ]);

    Xendivel::createQrCode(xendivelQrCodeRequest([
        'type' => 'STATIC',
        'amount' => '',
    ]));

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/payment_requests')
            && data_get($request->data(), 'payment_method.reusability') === 'MULTIPLE_USE'
            && ! array_key_exists('amount', $request->data());
    });
});

it('gets a qr payment request by id', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'id' => 'pr-123',
            'status' => 'SUCCEEDED',
        ]),
    ]);

    $qr = Xendivel::getQrCode('pr-123');

    expect($qr->getResponse()->status)->toBe('SUCCEEDED');

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'GET'
            && str_ends_with($request->url(), '/payment_requests/pr-123');
    });
});

it('simulates a qr payment with the provided amount', function () {
    Http::fake([
        'https://api.xendit.co/*' => Http::response([
            'status' => 'COMPLETED',
        ]),
    ]);

    Xendivel::simulateQrPayment('qr-ref-1', 2500);

    Http::assertSent(function (ClientRequest $request) {
        return $request->method() === 'POST'
            && str_ends_with($request->url(), '/qr_codes/qr-ref-1/payments/simulate')
            && $request['amount'] === 2500;
    });
});

it('resolves a payment request id to its qr reference before simulating', function () {
    Http::fake(function (ClientRequest $request) {
        if ($request->method() === 'GET') {
            return Http::response([
                'id' => 'pr-123',
                'payment_method' => ['reference_id' => 'resolved-ref'],
            ]);
        }

        return Http::response(['status' => 'COMPLETED']);
    });

    Xendivel::simulateQrPayment('pr-123', 2500);

    Http::assertSent(fn (ClientRequest $request) => $request->method() === 'GET'
        && str_ends_with($request->url(), '/payment_requests/pr-123'));

    Http::assertSent(fn (ClientRequest $request) => $request->method() === 'POST'
        && str_ends_with($request->url(), '/qr_codes/resolved-ref/payments/simulate')
        && $request['amount'] === 2500);
});

function xendivelQrCodeRequest(array $overrides = []): LaravelRequest
{
    return LaravelRequest::create('/create-qr-code', 'POST', array_merge([
        'external_id' => 'manual-qr-external-id',
        'type' => 'DYNAMIC',
        'amount' => 2500,
        'currency' => 'PHP',
    ], $overrides));
}
