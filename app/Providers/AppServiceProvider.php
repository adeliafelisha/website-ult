<?php

namespace App\Providers;

use App\Models\Contact;
use Illuminate\Support\Facades\Schema;
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
        View::composer('layouts.app', function ($view): void {
            $contacts = Schema::hasTable('contacts')
                ? Contact::query()->where('is_published', true)->orderBy('sort_order')->get()
                : collect();

            $view->with('footerContacts', $contacts);
        });
    }
}
