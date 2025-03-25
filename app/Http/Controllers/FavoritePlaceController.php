<?php

namespace App\Http\Controllers;

use App\Models\FavoritePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavoritePlaceController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'place_id' => 'required|exists:places,id'
            ]);

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Verificar si el lugar ya está en favoritos
            if ($user->favoritePlaces()->where('place_id', $request->place_id)->exists()) {
                return response()->json([
                    'message' => 'Este lugar ya está en tus favoritos'
                ], 409);
            }

            // Añadir a favoritos
            $user->favoritePlaces()->attach($request->place_id);

            return response()->json([
                'message' => 'Lugar añadido a favoritos correctamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al añadir lugar a favoritos: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
} 