<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/profil', [SiteController::class, 'profile'])->name('profile');
Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['id', 'en'], true), 404);
    session(['locale' => $locale]);

    return back();
})->name('language.switch');
Route::get('/layanan', [SiteController::class, 'services'])->name('services.index');
Route::get('/layanan/kategori/{category:slug}', [SiteController::class, 'serviceCategory'])->name('services.category');
Route::get('/layanan/{service:slug}', [SiteController::class, 'service'])->name('services.show');
Route::get('/artikel', [SiteController::class, 'articles'])->name('articles.index');
Route::get('/artikel/{article:slug}', [SiteController::class, 'article'])->name('articles.show');
Route::get('/faq', [SiteController::class, 'faqs'])->name('faqs');
Route::get('/kontak', [SiteController::class, 'contact'])->name('contact');
Route::get('/pencarian', [SiteController::class, 'search'])->middleware('throttle:30,1')->name('search');
Route::post('/analytics/outbound', [SiteController::class, 'track'])->middleware('throttle:60,1')->name('analytics.outbound');
