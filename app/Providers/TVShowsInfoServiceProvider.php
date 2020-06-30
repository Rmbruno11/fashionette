<?php

namespace App\Providers;

use App\Services\TVShowsInfoService;
use App\Services\TVMazeService;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

/**
 * Registers the service provider that will give movies information.
 */
class TVShowsInfoServiceProvider extends ServiceProvider
{
    public function register()
    {
        // TODO: can be extended to use a different service, for instance, by configuration.
        $this->app->bind(TVShowsInfoService::class, function ($app) {
            return new TVMazeService(new Client());
        });
    }
}