<?php

use GlennRaya\Xendivel\Services\OtcService;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'xendivel.auto_id' => false,
        'xendivel.redirects.failure_url' => null,
        'xendivel.redirects.success_url' => null,
        'xendivel.secret_key' => 'sk_test_123',
    ]);
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
