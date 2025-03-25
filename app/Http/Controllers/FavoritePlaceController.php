<?php

namespace App\Http\Controllers;

use App\Models\FavoritePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoritePlaceController extends Controller
{
    public function index()
    {
        $favoritePlaces = FavoritePlace::where('user_id', Auth::id())
            ->with('place')
            ->get()
            ->map(function ($favorite) {
                return $favorite->place;
            });

        return response()->json($favoritePlaces);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'place_id' => 'required|exists:places,id'
            ]);

            // Verificar si ya existe el favorito
            $exists = FavoritePlace::where('user_id', Auth::id())
                ->where('place_id', $request->place_id)
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'El lugar ya está en favoritos'], 409);
            }

            $favorite = FavoritePlace::create([
                'user_id' => Auth::id(),
                'place_id' => $request->place_id
            ]);

            return response()->json($favorite->place, 201);

        } catch (\Exception $e) {
            Log::error('Error al añadir lugar a favoritos: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $favorite = FavoritePlace::where('user_id', Auth::id())
            ->where('place_id', $id)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'Lugar favorito no encontrado'], 404);
        }

        $favorite->delete();
        return response()->json(['message' => 'Lugar eliminado de favoritos']);
    }
} 