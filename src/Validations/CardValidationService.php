<?php

namespace GlennRaya\Xendivel\Validations;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CardValidationService
{
    /**
     * Validate the card payment payload and provide custom error messages.
     */
    public static function validate(array $payload): void
    {
        $customMessages = [
            'amount.required' => 'The amount field is required.',
            'amount.integer' => 'The amount must be an integer.',
            'amount.min' => 'The amount must be at least 20.',
            'external_id.required' => 'Auto external_id is set to false in your config file. You need to supply your own external_id.',
            'token_id.required' => 'The token ID is required.',
            'token_id.string' => 'The token ID must be a string.',
        ];

        $validator = Validator::make($payload, [
            'amount' => 'required|integer|min:20',
            'external_id' => [
                Rule::when(config('xendivel.auto_external_id') === false, ['min:10', 'max:64', 'required']),
            ],
            'token_id' => 'required|string',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new Exception($errors);
        }
    }
}
