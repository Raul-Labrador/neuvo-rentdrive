<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CarLocationController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReturnController;

Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{slug}', [CarController::class, 'show']);

Route::get('/reservations', [ReservationController::class, 'getUserReservations']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::post('/availability', [AvailabilityController::class, 'check']);

Route::get('/cars/{id}/booked-dates', [CarController::class, 'bookedDates']);
Route::patch('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

Route::post('/returns', [ReturnController::class, 'store']);
Route::get('/returns/{reservation_id}', [ReturnController::class, 'showByReservation']);

Route::post('/incidents', [IncidentController::class, 'store']);

Route::any('/location-update', [CarLocationController::class, 'updateLocation']);
Route::get('/location/{car_id}', [CarLocationController::class, 'getLatestLocation']);