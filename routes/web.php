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
use App\Http\Controllers\UserCheckpointController;
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas que requieren autenticaciÃ³n
Route::middleware(['auth'])->group(function () {
    // Rutas de vistas
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::get('/api/gimcanas/{id}', [GimcanaController::class, 'showGimcana']);
    Route::post('/api/group/join', [GroupController::class, 'joinGroup']);
    Route::get('/api/user/group-status/{gimcanaId}', [GroupController::class, 'checkUserGroupStatus']);
    Route::post('/api/group/leave', [GroupController::class, 'leaveGroup']);
    Route::get('/api/group/{id}', [GroupController::class, 'show']);
    Route::get('/map/juego', [MapController::class, 'juego'])->name('map.juego');
    Route::get('/gimcana', [GimcanaController::class, 'showGimcanaForm'])->name('gimcana.form');
    Route::post('/gimcana/create', [GimcanaController::class, 'createGimcana'])->name('gimcana.create')->middleware('auth');
    Route::post('/gimcana/join', [GimcanaController::class, 'joinGimcana'])->name('gimcana.join')->middleware('auth');
    Route::get('/api/gimcanas', [GimcanaController::class, 'getGimcanas'])->name('gimcana.get')->middleware('auth');
    Route::get('/api/gimcanas/{id}/ready', [GimcanaController::class, 'checkIfGimcanaReady'])->name('gimcana.ready')->middleware('auth');
    Route::get('/api/checkpoints', [CheckpointController::class, 'index']);
    Route::get('/api/checkpoints/{checkpoint}/answers', [CheckpointController::class, 'getAnswers']);
    Route::post('/api/challenge-answers/verify', [ChallengeAnswerController::class, 'verifyAnswer']);
    Route::post('/api/user-checkpoints', [UserCheckpointController::class, 'store']);
    Route::get('/api/group/{group}/checkpoint/{checkpoint}/progress', [GroupController::class, 'checkCheckpointProgress']);
    Route::get('/api/user-checkpoints/completed', [UserCheckpointController::class, 'getCompleted']);
    Route::post('/api/gimcana/finish', [GimcanaController::class, 'finishGimcana']);

    // Rutas para Gimcanas
    Route::prefix('gimcanas')->group(function () {
        Route::get('/', [GimcanaController::class, 'index']);
        Route::post('/', [GimcanaController::class, 'store']);
        Route::get('/{gimcana}', [GimcanaController::class, 'show']);
        Route::put('/{gimcana}', [GimcanaController::class, 'update']);
        Route::delete('/{gimcana}', [GimcanaController::class, 'destroy']);
    });

    // Rutas para Places
    Route::get('/places', [PlaceController::class, 'index']);
    Route::post('/places', [PlaceController::class, 'store']);
    Route::get('/places/{place}', [PlaceController::class, 'show']);
    Route::put('/places/{place}', [PlaceController::class, 'update']);
    Route::delete('/places/{place}', [PlaceController::class, 'destroy']);

    // API Routes
    Route::prefix('api')->group(function () {
        Route::apiResource('places', PlaceController::class);
        Route::apiResource('gimcanas', GimcanaController::class);
        Route::apiResource('checkpoints', CheckpointController::class);
        Route::get('/tags', [TagController::class, 'index']);
    });
});
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

Route::middleware(['auth'])->group(function () {
    //Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    Route::get('/checkpoints', [CheckpointController::class, 'index']);
    Route::post('/checkpoints', [CheckpointController::class, 'store']);
    Route::get('/checkpoints/{checkpoint}', [CheckpointController::class, 'show']);
    Route::delete('/checkpoints/{checkpoint}', [CheckpointController::class, 'destroy']);

    Route::get('/checkpoints/{checkpoint}/answers', [ChallengeAnswerController::class, 'index']);
    Route::post('/challenge-answers', [ChallengeAnswerController::class, 'store']);
    Route::post('/challenge-answers/verify', [ChallengeAnswerController::class, 'verifyAnswer']);
    Route::put('/places/{place}/tags', [PlaceController::class, 'updateTags']);
});