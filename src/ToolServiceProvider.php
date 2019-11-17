<?php

namespace Just\Warehouse\Nova\Scan;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Just\Warehouse\Nova\Scan\Http\Middleware\Authorize;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nova-warehouse-scan');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::provideToScript(array_filter([
                'productInformationEndpoint' => config('warehouse.product.information.endpoint'),
                'redirectAfterOrderFulfillement' => config('warehouse.redirect.after.fulfillment'),
            ]));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware([
                'nova',
                Authorize::class,
                SubstituteBindings::class,
            ])
            ->prefix('nova-vendor/mvdnbrk/warehouse-scan')
            ->group(__DIR__.'/../routes/api.php');
    }
}
