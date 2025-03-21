<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GimcanaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CheckpointController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Rutas protegidas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Ruta del dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });

    // Rutas para Gimcanas
    Route::get('/gimcanas', [GimcanaController::class, 'index']);
    Route::post('/gimcanas', [GimcanaController::class, 'store']);


Route::middleware(['auth'])->group(function () {
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::put('/places/{place}', [PlaceController::class, 'update']);
    Route::delete('/places/{place}', [PlaceController::class, 'destroy']);
});
