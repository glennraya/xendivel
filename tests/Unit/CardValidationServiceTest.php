<?php

use GlennRaya\Xendivel\Validations\CardValidationService;

it('allows card payloads without an external id when auto id is enabled', function () {
    config(['xendivel.auto_id' => true]);

    CardValidationService::validate([
        'amount' => 2500,
        'token_id' => 'token-123',
        'authentication_id' => 'auth-123',
    ]);

    expect(true)->toBeTrue();
});

it('requires a manual external id when auto id is disabled', function () {
    config(['xendivel.auto_id' => false]);

    expect(fn () => CardValidationService::validate([
        'amount' => 2500,
        'token_id' => 'token-123',
        'authentication_id' => 'auth-123',
    ]))->toThrow(Exception::class, 'Auto external_id is set to false');
});

it('rejects invalid card payment payloads', function (array $payload, string $message) {
    config(['xendivel.auto_id' => false]);

    expect(fn () => CardValidationService::validate($payload))
        ->toThrow(Exception::class, $message);
})->with([
    'missing amount' => [
        [
            'external_id' => 'manual-external-id',
            'token_id' => 'token-123',
            'authentication_id' => 'auth-123',
        ],
        'The amount field is required.',
    ],
    'amount below Xendit minimum' => [
        [
            'amount' => 19,
            'external_id' => 'manual-external-id',
            'token_id' => 'token-123',
            'authentication_id' => 'auth-123',
        ],
        'The amount must be at least 20.',
    ],
    'missing token id' => [
        [
            'amount' => 2500,
            'external_id' => 'manual-external-id',
            'authentication_id' => 'auth-123',
        ],
        'The token ID is required.',
    ],
    'missing authentication id' => [
        [
            'amount' => 2500,
            'external_id' => 'manual-external-id',
            'token_id' => 'token-123',
        ],
        'The authentication ID is required',
    ],
]);
