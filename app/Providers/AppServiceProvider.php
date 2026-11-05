<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            view()->composer('*', function ($view) {
                $view->with([
                    'siteName' => \App\Models\SiteSetting::getSiteName(),
                    'siteTagline' => \App\Models\SiteSetting::getTagline(),
                    'siteLogoUrl' => \App\Models\SiteSetting::getLogoUrl(),
                    'contactEmail' => \App\Models\SiteSetting::get('contact_email', 'support@bazaario.in'),
                    'contactPhone' => \App\Models\SiteSetting::get('contact_phone', '+91 800-BAZAARIO'),
                ]);
            });
        } catch (\Throwable $e) {
            // Ignore during migrations or initial console boots
        }
    }
}
