<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Place::with('tags');

            // Filtrar por etiqueta si se proporciona
            if ($request->has('tag_id')) {
                $query->whereHas('tags', function($q) use ($request) {
                    $q->where('tags.id', $request->tag_id);
                });
            }

            $places = $query->get();
            return response()->json($places);
        } catch (\Exception $e) {
            Log::error('Error fetching places: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching places'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'icon' => 'nullable|string',
                'tags' => 'nullable|array',
                'tags.*' => 'exists:tags,id'
            ]);

        $place = Place::create($validatedData);

            $place = Place::create($request->except('tags'));

            // Asignar etiquetas si se proporcionan
            if ($request->has('tags')) {
                $place->tags()->sync($request->tags);
            }

            Log::info('Place created successfully: ' . $place->id);
            return response()->json($place->load('tags'), 201);

        } catch (\Exception $e) {
            Log::error('Error creating place: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating place'], 500);
        }

        return response()->json($place->load('tags'), 201);
    }

    public function show(Place $place)
    {
        try {
            return response()->json($place->load('tags'));
        } catch (\Exception $e) {
            Log::error('Error fetching place: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching place'], 500);
        }
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

    public function updateTags(Request $request, Place $place)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $place->tags()->sync($request->tags);
            Log::info('Place tags updated successfully: ' . $place->id);
            return response()->json($place->load('tags'));

        } catch (\Exception $e) {
            Log::error('Error updating place tags: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating place tags'], 500);
        }
    }
}