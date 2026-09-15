<?php

namespace Workbench\App\Providers;

use Heritage\Support\Facades\Route;
use Heritage\Support\Facades\View;
use Heritage\Support\ServiceProvider;
use Ugarit\Folio\Folio;

class FolioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::view('/', 'welcome');

        View::addLocation(__DIR__.'/../../../tests/Feature/resources/views');

        Folio::path(__DIR__.'/../../../tests/Feature/resources/views/pages')->middleware([
            '*' => [
                //
            ],
        ]);
    }
}
