<?php

namespace GlennRaya\Xendivel\Validations;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QrCodeValidationService
{
    /**
     * Validate the QR code payload and provide custom error messages.
     */
    public static function validate(array $payload): void
    {
        $customMessages = [
            'type.required' => 'The QR code type is required.',
            'type.in' => 'The QR code type must be either DYNAMIC or STATIC.',
            'amount.required_if' => 'The amount is required for DYNAMIC QR codes.',
            'amount.integer' => 'The amount must be an integer.',
            'amount.min' => 'The amount must be at least 1.',
            'external_id.required' => 'Auto external_id is set to false in your config file. You need to supply your own external_id.',
            'currency.string' => 'The currency must be a string.',
            'channel_code.string' => 'The channel code must be a string.',
        ];

        $validator = Validator::make($payload, [
            'type' => 'required|in:DYNAMIC,STATIC',
            'amount' => 'required_if:type,DYNAMIC|nullable|integer|min:1',
            'external_id' => [
                Rule::when(config('xendivel.auto_id') === false, ['min:10', 'max:64', 'required']),
            ],
            'currency' => 'nullable|string',
            'channel_code' => 'nullable|string',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new Exception($errors);
        }
    }
}
