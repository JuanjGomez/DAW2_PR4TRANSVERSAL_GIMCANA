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

Route::get('/gimcana', [GimcanaController::class, 'showGimcanaForm'])->name('gimcana.form')->middleware('auth');
Route::post('/gimcana/create', [GimcanaController::class, 'createGimcana'])->name('gimcana.create')->middleware('auth');
Route::post('/gimcana/join', [GimcanaController::class, 'joinGimcana'])->name('gimcana.join')->middleware('auth');
<<<<<<< HEAD
=======
Route::get('/api/gimcanas', [GimcanaController::class, 'getGimcanas'])->name('gimcana.get')->middleware('auth');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth');


Route::middleware(['auth'])->group(function () {
    // Ruta del dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
>>>>>>> aina

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth');

<<<<<<< HEAD
Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard')->middleware('auth');
=======
    // Rutas para Places
    Route::get('/places', [PlaceController::class, 'index']);
    Route::post('/places', [PlaceController::class, 'store']);
    Route::get('/places/{place}', [PlaceController::class, 'show']);
    Route::put('/places/{place}', [PlaceController::class, 'update']);
    Route::delete('/places/{place}', [PlaceController::class, 'destroy']);
});
>>>>>>> aina
