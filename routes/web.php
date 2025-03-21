<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GimcanaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\ChallengeAnswerController;

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

Route::get('/api/gimcanas', [GimcanaController::class, 'getGimcanas'])->name('gimcana.get')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });


Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth');

    // Rutas para Places
    Route::get('/places', [PlaceController::class, 'index']);
    Route::post('/places', [PlaceController::class, 'store']);
    Route::get('/places/{place}', [PlaceController::class, 'show']);
    Route::put('/places/{place}', [PlaceController::class, 'update']);
    Route::delete('/places/{place}', [PlaceController::class, 'destroy']);
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
});
