<?php

use App\Http\Controllers\Admin\EventSettingsController;
use App\Http\Controllers\Admin\FoodOptionsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AfterMoviesController;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Admin\RegistrationsController;
use App\Http\Controllers\CandidateRegistrationsController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SpectatorRegistrationsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/partenaires', [PublicController::class, 'partners'])->name('partners');
Route::get('/mentions-legales', [PublicController::class, 'legalNotice'])->name('legal.notice');
Route::get('/confidentialite', [PublicController::class, 'privacy'])->name('legal.privacy');

Route::get('/inscription/spectateurs', [PublicController::class, 'spectators'])->name('registrations.spectators');
Route::post('/inscription/spectateurs', [SpectatorRegistrationsController::class, 'store'])->name('registrations.spectators.store');

Route::get('/inscription/candidats', [PublicController::class, 'candidates'])->name('registrations.candidates');
Route::post('/inscription/candidats', [CandidateRegistrationsController::class, 'store'])->name('registrations.candidates.store');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'can:admin'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [EventSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [EventSettingsController::class, 'update'])->name('settings.update');

    Route::get('/after-movies', [AfterMoviesController::class, 'index'])->name('after-movies.index');
    Route::post('/after-movies', [AfterMoviesController::class, 'store'])->name('after-movies.store');
    Route::patch('/after-movies/{afterMovie}', [AfterMoviesController::class, 'update'])->name('after-movies.update');
    Route::delete('/after-movies/{afterMovie}', [AfterMoviesController::class, 'destroy'])->name('after-movies.destroy');

    Route::get('/partners', [PartnersController::class, 'index'])->name('partners.index');
    Route::post('/partners', [PartnersController::class, 'store'])->name('partners.store');
    Route::patch('/partners/{partner}', [PartnersController::class, 'update'])->name('partners.update');
    Route::delete('/partners/{partner}', [PartnersController::class, 'destroy'])->name('partners.destroy');

    Route::get('/food-options', [FoodOptionsController::class, 'index'])->name('food-options.index');
    Route::post('/food-options', [FoodOptionsController::class, 'store'])->name('food-options.store');
    Route::patch('/food-options/{foodOption}', [FoodOptionsController::class, 'update'])->name('food-options.update');
    Route::delete('/food-options/{foodOption}', [FoodOptionsController::class, 'destroy'])->name('food-options.destroy');

    Route::get('/registrations/spectators', [RegistrationsController::class, 'spectators'])->name('registrations.spectators');
    Route::get('/registrations/spectators/export', [RegistrationsController::class, 'exportSpectators'])->name('registrations.spectators.export');

    Route::get('/registrations/candidates', [RegistrationsController::class, 'candidates'])->name('registrations.candidates');
    Route::get('/registrations/candidates/export', [RegistrationsController::class, 'exportCandidates'])->name('registrations.candidates.export');
    Route::get('/registrations/candidates/{candidateRegistration}/download-text', [RegistrationsController::class, 'downloadCandidateText'])->name('registrations.candidates.download-text');
    Route::get('/registrations/candidates/{candidateRegistration}/download-photo', [RegistrationsController::class, 'downloadCandidatePhoto'])->name('registrations.candidates.download-photo');
});

require __DIR__.'/settings.php';
