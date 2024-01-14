<?php

use GlennRaya\Xendivel\Xendivel;
use Tests\TestCase;

uses(TestCase::class);

describe('Xendivel Feature: ', function () {
    it('can generate a PDF invoice.', function () {
        $response = $this->get('/xendivel/invoice/generate');

        $response->assertSeeText('storage/app/invoices/');
    });

    it('can download the example invoice.', function () {
        $response = $this->get('/xendivel/invoice/download');
        $response->assertHeader('Content-Type', 'application/pdf');
    });

});
