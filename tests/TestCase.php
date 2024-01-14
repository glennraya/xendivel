<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getPackageProviders($app)
    {
        return [
            // ... other service providers
            \GlennRaya\Xendivel\XendivelServiceProvider::class,
        ];
    }

}
