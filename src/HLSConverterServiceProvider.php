<?php
namespace Juzaweb\HLSConverter;

use Illuminate\Support\ServiceProvider;

class HLSConverterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(HLSConverterService::class, function () {
            return new HLSConverterService();
        });
    }

    public function boot()
    {
        //
    }
}
