<?php

namespace Juzaweb\HLSConverter\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Juzaweb\HLSConverter\HLSConverterServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            HLSConverterServiceProvider::class,
        ];
    }
}
