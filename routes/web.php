<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GimcanaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\ChallengeAnswerController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\FavoritePlaceController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Rutas de vistas
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    // Rutas de la API
    Route::prefix('api')->group(function () {
        Route::get('/places', [PlaceController::class, 'index']);
        Route::get('/places/distance', [PlaceController::class, 'getPlacesByDistance']);
        Route::get('/tags', [TagController::class, 'index']);
        Route::get('/gimcanas', [GimcanaController::class, 'index']);
        Route::get('/gimcanas/{id}', [GimcanaController::class, 'showGimcana']);
        Route::post('/group/join', [GroupController::class, 'joinGroup']);
        Route::get('/user/group-status/{gimcanaId}', [GroupController::class, 'checkUserGroupStatus']);
        Route::post('/group/leave', [GroupController::class, 'leaveGroup']);
        Route::get('/favorite-places', [FavoritePlaceController::class, 'index']);
        Route::post('/favorite-places', [FavoritePlaceController::class, 'store']);
        Route::delete('/favorite-places/{id}', [FavoritePlaceController::class, 'destroy']);
    });

    // Rutas de recursos
    Route::apiResource('places', PlaceController::class);
    Route::apiResource('gimcanas', GimcanaController::class);
    Route::apiResource('checkpoints', CheckpointController::class);
    Route::apiResource('tags', TagController::class);
    
    // Rutas adicionales
    Route::get('/checkpoints/{checkpoint}/answers', [ChallengeAnswerController::class, 'index']);
    Route::post('/challenge-answers', [ChallengeAnswerController::class, 'store']);
    Route::post('/challenge-answers/verify', [ChallengeAnswerController::class, 'verifyAnswer']);
    Route::put('/places/{place}/tags', [PlaceController::class, 'updateTags']);
});
