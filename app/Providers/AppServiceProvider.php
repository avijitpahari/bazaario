<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\Support\SectionHeader;
use App\View\Components\Support\Tabs;
use App\View\Components\Support\Avatar;
use App\View\Components\Support\Pill;
use App\View\Components\Support\Bubble;
use App\View\Components\Support\Card;
use App\View\Components\Support\EmptyState;
use App\View\Components\Support\DecisionCard;
use App\View\Components\Support\SearchFilter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom Blade directive for Indian Rupee formatting
        Blade::directive('inr', function ($expression) {
            return "<?php echo '₹' . number_format((float)({$expression}), 2); ?>";
        });
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

        // Register support namespace components for Blade
        // This allows using <x-support::component-name> syntax in Laravel 12
        // Using Blade::component() with namespace and component name
        Blade::component('support::section-header', SectionHeader::class);
        Blade::component('support::tabs', Tabs::class);
        Blade::component('support::avatar', Avatar::class);
        Blade::component('support::pill', Pill::class);
        Blade::component('support::bubble', Bubble::class);
        Blade::component('support::card', Card::class);
        Blade::component('support::empty-state', EmptyState::class);
        Blade::component('support::decision-card', DecisionCard::class);
        Blade::component('support::search-filter', SearchFilter::class);
    }
}