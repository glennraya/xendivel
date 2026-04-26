<?php

use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

describe('Xendivel example routes', function () {
    it('loads the example checkout template (Blade).', function () {
        $this->get('/xendivel/checkout/blade')
            ->assertOk()
            ->assertSeeText('Xendivel Cards Payment Template');
    });

    it('loads the example invoice template.', function () {
        $this->get('/xendivel/invoice/template')
            ->assertOk()
            ->assertSeeText('Xendivel Invoice Template');
    });
});

describe('Xendivel payment return routes', function () {
    it('loads the payment return pages', function (string $uri, string $expected_text) {
        $this->get($uri)
            ->assertOk()
            ->assertSeeText($expected_text);
    })->with([
        'success' => ['/xendivel/payment/success', 'Payment received'],
        'failed' => ['/xendivel/payment/failed', 'Payment not completed'],
    ]);

    it('registers named return routes', function () {
        expect(route('xendivel.payment.success', [], false))->toBe('/xendivel/payment/success')
            ->and(route('xendivel.payment.failed', [], false))->toBe('/xendivel/payment/failed');
    });

    it('does not register a fallback route', function () {
        expect(collect(Route::getRoutes())->contains(fn ($route) => $route->isFallback))->toBeFalse();
    });
});

describe('Xendivel card demo routes', function () {
    beforeEach(function () {
        config([
            'xendivel.auto_id' => false,
            'xendivel.secret_key' => 'sk_test_123',
        ]);
    });

    it('authorizes cards through the demo authorize route', function () {
        Http::fake([
            'https://api.xendit.co/*' => Http::response([
                'id' => 'card-auth-123',
                'status' => 'AUTHORIZED',
            ]),
        ]);

        $this->postJson('/authorize-card', [
            'amount' => 2500,
            'external_id' => 'manual-external-id',
            'token_id' => 'token-123',
            'authentication_id' => 'auth-123',
        ])->assertOk()
            ->assertJsonPath('status', 'AUTHORIZED');

        Http::assertSent(fn (ClientRequest $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges')
            && $request['capture'] === false);
    });

    it('captures card authorizations through the demo capture route', function () {
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
            ]);
        });

        $this->postJson('/capture-card-charge', [
            'charge_id' => 'card-charge-123',
            'amount' => 1800,
        ])->assertOk()
            ->assertJsonPath('status', 'CAPTURED');

        Http::assertSent(fn (ClientRequest $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges/card-charge-123/capture')
            && $request['amount'] === 1800);
    });

    it('voids card authorizations through the demo void route', function () {
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

        $this->postJson('/void-card-authorization', [
            'charge_id' => 'card-charge-123',
            'external_id' => 'void-external-id',
        ])->assertOk()
            ->assertJsonPath('status', 'REVERSED');

        Http::assertSent(fn (ClientRequest $request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/credit_card_charges/card-charge-123/auth_reversal')
            && $request['external_id'] === 'void-external-id');
    });
});
