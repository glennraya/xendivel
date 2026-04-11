<?php

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
