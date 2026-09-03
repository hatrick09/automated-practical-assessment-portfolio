<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Belt-and-suspenders alongside trustProxies() in bootstrap/app.php: on
        // Railway/Render/Heroku-style hosts behind an HTTPS-terminating proxy,
        // force every generated URL (route(), url(), asset(), form actions) to
        // use https:// so nothing ever renders as an insecure http:// link.
        if (config('app.env') === 'production' || env('FORCE_HTTPS') === 'true') {
            URL::forceScheme('https');
        }
    }
}
