<?php

namespace Tests;

use GlennRaya\Xendivel\XendivelServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            XendivelServiceProvider::class,
        ];
    }
}
