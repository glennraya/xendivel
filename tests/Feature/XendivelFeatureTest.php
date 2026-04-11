<?php

describe('Xendivel webhook route', function () {
    it('rejects unsigned webhook callbacks by default', function () {
        $response = $this->post(config('xendivel.webhook_url'), [
            'event' => 'ewallet.capture',
        ]);

        $response->assertForbidden();
    });
});
