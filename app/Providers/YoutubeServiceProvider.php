<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class YoutubeServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(base_path() . '/config/youtube.php');

        $this->publishes([$source => config_path('youtube.php')]);

        $this->mergeConfigFrom($source, 'youtube');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Youtube::class, function () {
            return new Youtube(config('youtube.key'));
        });

        $this->app->alias(Youtube::class, 'youtube');
    }

    /**
     * Get service from provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Youtube::class];
    }
}
