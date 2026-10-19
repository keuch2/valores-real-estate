<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\LegacyRedirectController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/nosotros', [SiteController::class, 'about'])->name('about');
Route::get('/contacto', [SiteController::class, 'contact'])->name('contact');
Route::get('/trabaja-con-nosotros', [SiteController::class, 'careers'])->name('careers');
Route::get('/investor-pass', [SiteController::class, 'investor'])->name('investor');

Route::get('/proyectos', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/proyectos/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');
Route::get('/noticias/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::middleware(['throttle:forms', 'spam'])->group(function () {
    Route::post('/contacto', [FormController::class, 'contact'])->name('contact.store');
    Route::post('/trabaja-con-nosotros', [FormController::class, 'careers'])->name('careers.store');
    Route::post('/investor-pass', [FormController::class, 'investor'])->name('investor.store');
    Route::post('/proyectos/{slug}/consulta', [FormController::class, 'project'])->name('projects.inquiry');
});

// URLs del sitio PHP original → 301 a las nuevas.
Route::get('/{page}.php', LegacyRedirectController::class)->where('page', '[a-z\-]+');
Route::get('/assets/brochures/{slug}.pdf', [LegacyRedirectController::class, 'brochure']);
