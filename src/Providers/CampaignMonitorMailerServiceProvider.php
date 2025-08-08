<?php

namespace CampaignMonitorMailer\Providers;

use CampaignMonitorMailer\Transport\CampaignMonitorTransport;
use Illuminate\Support\ServiceProvider;

class CampaignMonitorMailerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/campaign-monitor.php',
            'campaign-monitor'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Campaign Monitor transport
        $this->app->make('mail.manager')->extend('campaign-monitor', function () {
            return new CampaignMonitorTransport();
        });

        // Publish config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/campaign-monitor.php' => config_path('campaign-monitor.php'),
            ], 'campaign-monitor-config');
        }
    }
}
