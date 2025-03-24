<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    public function index()
    {
        return Place::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'icon' => 'required|string|max:255',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $place = Place::create($validatedData);

        // Asociar tags si se proporcionan
        if ($request->has('tags')) {
            $place->tags()->sync($request->tags);
        }

        return response()->json($place->load('tags'), 201);
    }

    public function show(Place $place)
    {
        return $place->load('tags');
    }

    public function update(Request $request, Place $place)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'icon' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $place->update($validatedData);

        // Sincronizar tags si se proporcionan
        if ($request->has('tags')) {
            $place->tags()->sync($request->tags);
        }

        return response()->json($place->load('tags'));
    }

    public function destroy(Place $place)
    {
        DB::beginTransaction();

        try {
            // Eliminar las relaciones en place_tag
            $place->tags()->detach();
            
            // Eliminar el lugar
            $place->delete();

            DB::commit();

            // Devolver una respuesta JSON válida
            return response()->json(['message' => 'Lugar eliminado correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar el lugar: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar el lugar'], 500);
        }
    }
}