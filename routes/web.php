<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GimcanaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\ChallengeAnswerController;
use App\Http\Controllers\TagController;

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
    // Ruta del dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::get('/api/gimcanas', [GimcanaController::class, 'getGimcanas']);
    Route::get('/api/gimcanas/{id}', [GimcanaController::class, 'showGimcana']);
    Route::post('/api/group/join', [GroupController::class, 'joinGroup']);
    Route::get('/gimcana/{id}/check-ready', [GimcanaController::class, 'checkIfGimcanaReady'])->name('gimcana.checkReady');
    
    Route::get('/api/user/group-status/{gimcanaId}', [GroupController::class, 'checkUserGroupStatus']);
    Route::post('/api/group/leave', [GroupController::class, 'leaveGroup']);
    Route::get('/gimcana', [GimcanaController::class, 'showGimcanaForm'])->name('gimcana.form');
    Route::post('/gimcana/create', [GimcanaController::class, 'createGimcana'])->name('gimcana.create')->middleware('auth');
    Route::post('/gimcana/join', [GimcanaController::class, 'joinGimcana'])->name('gimcana.join')->middleware('auth');
    Route::get('/api/gimcanas', [GimcanaController::class, 'getGimcanas'])->name('gimcana.get')->middleware('auth');

    // Rutas para Gimcanas
    Route::get('/gimcanas', [GimcanaController::class, 'index']);
    Route::post('/gimcanas', [GimcanaController::class, 'store']);

    // Rutas para Places
    Route::get('/places', [PlaceController::class, 'index']);
    Route::post('/places', [PlaceController::class, 'store']);
    Route::get('/places/{place}', [PlaceController::class, 'show']);
    Route::put('/places/{place}', [PlaceController::class, 'update']);
    Route::delete('/places/{place}', [PlaceController::class, 'destroy']);

    // Rutas de grupo
    Route::get('/group/{id}', [GroupController::class, 'showGroup'])->name('group.show');
    Route::post('/group/join', [GroupController::class, 'joinGroup'])->name('group.join');
    Route::post('/group/{id}/leave', [GroupController::class, 'leaveGroup'])->name('group.leave');

    // API Routes
    Route::prefix('api')->group(function () {
        Route::apiResource('places', PlaceController::class);
        Route::apiResource('gimcanas', GimcanaController::class);
        Route::apiResource('checkpoints', CheckpointController::class);
        Route::get('/tags', [TagController::class, 'index']);
        Route::post('/favorite-places', [FavoritePlaceController::class, 'store'])->middleware('auth:sanctum');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    Route::get('/gimcanas', [GimcanaController::class, 'index']);
    Route::post('/gimcanas', [GimcanaController::class, 'store']);
    Route::get('/gimcanas/{gimcana}', [GimcanaController::class, 'show']);
    Route::put('/gimcanas/{gimcana}', [GimcanaController::class, 'update']);
    Route::delete('/gimcanas/{gimcana}', [GimcanaController::class, 'destroy']);

    Route::get('/places', [PlaceController::class, 'index']);
    Route::post('/places', [PlaceController::class, 'store']);
    Route::get('/places/{place}', [PlaceController::class, 'show']);
    Route::delete('/places/{place}', [PlaceController::class, 'destroy']);

    Route::get('/checkpoints', [CheckpointController::class, 'index']);
    Route::post('/checkpoints', [CheckpointController::class, 'store']);
    Route::get('/checkpoints/{checkpoint}', [CheckpointController::class, 'show']);
    Route::delete('/checkpoints/{checkpoint}', [CheckpointController::class, 'destroy']);

    Route::get('/checkpoints/{checkpoint}/answers', [ChallengeAnswerController::class, 'index']);
    Route::post('/challenge-answers', [ChallengeAnswerController::class, 'store']);
    Route::post('/challenge-answers/verify', [ChallengeAnswerController::class, 'verifyAnswer']);

    // Rutas para etiquetas
    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
    Route::get('/tags/{tag}/places', [TagController::class, 'getPlacesByTag']);

    // Ruta para actualizar etiquetas de un lugar
    Route::put('/places/{place}/tags', [PlaceController::class, 'updateTags']);
});

Route::get('/map', [MapController::class, 'index'])->name('map.index');
