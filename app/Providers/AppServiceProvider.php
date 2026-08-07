<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
    Paginator::useBootstrapFive();

    if ($this->app->environment('testing') && \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite') {
        try {
            \Illuminate\Support\Facades\DB::getDoctrineSchemaManager()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('enum', 'string');
        } catch (\Exception $e) {
            // Ignore if doctrine isn't fully loaded
        }
    }
  }
}
