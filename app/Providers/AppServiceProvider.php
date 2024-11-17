<?php

namespace App\Providers;

use App\Models\StorageItem;
use App\Observers\StorageItemObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

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
        if (config('app.force_https', false) || !app()->environment('local', 'testing')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);

        $this->observers();
    }

    public function observers(): void
    {
        StorageItem::observe(StorageItemObserver::class);
    }
}
