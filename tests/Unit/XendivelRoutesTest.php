<?php

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
