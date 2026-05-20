<?php

use App\Models\Car;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IncidentController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Auth\WordPressSsoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// SSO desde WordPress 
// Ruta pública (sin middleware 'guest') para que el admin de WP entre directamente.
Route::get('/wp-sso', [WordPressSsoController::class, 'handle'])->name('wp.sso');
Route::get('/wp-sso-logout', [WordPressSsoController::class, 'logout'])->name('wp.sso.logout');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('cars', CarController::class);
    Route::delete('cars/{car}/media/{media}', [CarController::class, 'destroyMedia'])->name('cars.destroyMedia');

    Route::resource('clients', ClientController::class)->except(['show']);

    Route::get('incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('incidents/{id}', [IncidentController::class, 'show'])->name('incidents.show');
    Route::patch('incidents/{id}', [IncidentController::class, 'update'])->name('incidents.update');

    Route::post('returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('returns/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::get('returns/{reservation}', [ReturnController::class, 'show'])->name('returns.show');

    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
});

require __DIR__ . '/auth.php';


Route::get('/mapa-en-vivo', function () {
    return view('admin.map');
});

Route::get('/admin/cars/{car}/map', function (Car $car) {
    return view('admin.map', ['car' => $car]);
})->name('admin.cars.map');