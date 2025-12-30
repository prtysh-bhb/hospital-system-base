<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
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
        // Register @hasAccess directive for checking boolean settings
        Blade::directive('hasAccess', function ($expression) {
            return "<?php if(app('settings')->get($expression, false)): ?>";
        });

        Blade::directive('endhasAccess', function () {
            return '<?php endif; ?>';
        });

        // Share settings globally
        $this->app->singleton('settings', function () {
            $settings = Setting::pluck('value', 'key');

            return collect($settings)->map(function ($value) {
                // Convert '1' and '0' to boolean for boolean settings
                if ($value === '1') {
                    return true;
                }
                if ($value === '0') {
                    return false;
                }

                return $value;
            });
        });

        // Share settings with all views
        view()->composer('*', function ($view) {
            $view->with('formSettings', app('settings')->all());
        });
    }
}
