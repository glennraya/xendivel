<?php

use Tests\TestCase;

uses(TestCase::class);

describe('Xendivel Template: ', function () {
    it('loads the example checkout template (Blade).', function () {
        $response = $this->get('/xendivel/checkout/blade');

        $response->assertStatus(200);
    });

    it('loads the example invoice template.', function () {
        $response = $this->get('/xendivel/invoice/template');

        $response->assertStatus(200);
    });
});
