<?php
namespace Juzaweb\HLSConverter;

use Illuminate\Support\ServiceProvider;

class HLSConverterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(HLSConverter::class, function () {
            return new HLSConverterService();
        });
    }

    public function boot()
    {
        //
    }
}
