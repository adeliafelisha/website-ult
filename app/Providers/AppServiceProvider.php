<?php

namespace App\Providers;

use App\Models\Contact;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        // Vercel terminates TLS before forwarding the request to PHP. Force the
        // public scheme so Vite, Filament, pagination, and signed URLs never
        // generate mixed-content HTTP assets in production.
        if (env('VERCEL')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view): void {
            $contacts = Schema::hasTable('contacts')
                ? Contact::query()->where('is_published', true)->orderBy('sort_order')->get()
                : collect();

            $view->with('footerContacts', $contacts);
        });
    }
}
