<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class MobileProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        envWrite('APP_URL', URL::to('/'));
        envWrite('MIX_ASSET_URL', URL::to('/').'/public');
        envWrite('MOBILE_MODE', 'off');
    }
}

