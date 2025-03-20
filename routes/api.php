use App\Http\Controllers\PlaceController;

Route::get('/places', [PlaceController::class, 'index']); 