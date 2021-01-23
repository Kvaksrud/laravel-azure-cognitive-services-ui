<?php
namespace Kvaksrud\AzureCognitiveServices\Ui;
use Illuminate\Support\ServiceProvider;
use Kvaksrud\AzureCognitiveServices\Ui\App\Console\Commands\AcsLpgTrainingStatus;
use Kvaksrud\AzureCognitiveServices\Ui\App\Console\Commands\AcsSyncLpgFromAzure;

class AzureCognitiveServicesUiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publishing files to solution
        $this->publishes([
            __DIR__.'/../config/azure-cognitive-services-ui.php' => config_path('azure-cognitive-services-ui.php'),
        ]);

        // Database migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Routes
        if(config('azure-cognitive-services-ui.general.enable_routes',true) === true) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'azure-cognitive-services-ui');

        // Console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                AcsLpgTrainingStatus::class, // acs:updateLpgTrainingStatus {force?}
                AcsSyncLpgFromAzure::class, // acs:updateLpgTrainingStatus {force?}
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/azure-cognitive-services-ui.php', 'azure-cognitive-services-ui'
        );
    }
}
