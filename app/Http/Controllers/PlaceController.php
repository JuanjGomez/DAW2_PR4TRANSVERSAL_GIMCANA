<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PlaceController extends Controller
{
    public function index()
    {
        // Cache por 5 minutos
        $places = Cache::remember('places.all', 300, function () {
            return Place::with('tags')->get();
        });

        return response()->json($places);
    }

    public function getPlacesByDistance(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $distance = $request->input('distance', 5); // Distancia en km

        $cacheKey = "places.distance.{$latitude}.{$longitude}.{$distance}";
        
        $places = Cache::remember($cacheKey, 60, function () use ($latitude, $longitude, $distance) {
            return Place::with('tags')
                ->selectRaw("
                    places.*,
                    (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                    sin(radians(latitude)))) AS distance
                ", [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $distance)
                ->orderBy('distance')
                ->get();
        });

        return response()->json($places);
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

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $validatedData = $validator->validated();
            $place = Place::create($validatedData);

            // Asignar etiquetas si se proporcionan
            if ($request->has('tags')) {
                $place->tags()->sync($request->tags);
            }

            Log::info('Place created successfully: ' . $place->id);
            return response()->json($place->load('tags'), 201);

        } catch (\Exception $e) {
            Log::error('Error creating place: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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