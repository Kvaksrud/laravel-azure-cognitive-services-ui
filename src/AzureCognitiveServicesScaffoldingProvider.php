<?php
namespace Kvaksrud\AzureCognitiveServices\Scaffolding;
use Illuminate\Support\ServiceProvider;

class AzureCognitiveServicesScaffoldingProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/azure-cognitive-services-scaffolding.php' => config_path('azure-cognitive-services-scaffolding.php'),
            __DIR__.'/../resources/views' => resource_path('views/azure-cognitive-services'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/azure-cognitive-services-scaffolding.php', 'laravel-azure-cognetive-services-scaffolding'
        );
    }
}
