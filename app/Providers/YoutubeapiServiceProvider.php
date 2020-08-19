<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class YoutubeapiServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(base_path() . '/config/youtubeapi.php');

        $this->publishes([$source => config_path('youtubeapi.php')]);

        $this->mergeConfigFrom($source, 'youtubeapi');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Youtubeapi::class, function () {
            return new Youtubeapi(config('youtubeapi.key'));
        });

        $this->app->alias(Youtubeapi::class, 'youtubeapi');
    }

    /**
     * Get service from provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Youtubeapi::class];
    }
}
