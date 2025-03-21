<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlaceController extends Controller
{
    public function index()
    {
        try {
            $places = Place::all();
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
                'icon' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $place = Place::create($request->all());
            Log::info('Place created successfully: ' . $place->id);
            return response()->json($place, 201);

        } catch (\Exception $e) {
            Log::error('Error creating place: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating place'], 500);
        }
    }

    public function show(Place $place)
    {
        try {
            return response()->json($place);
        } catch (\Exception $e) {
            Log::error('Error fetching place: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching place'], 500);
        }
    }

    public function update(Request $request, Place $place)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'icon' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $place->update($request->all());
        return response()->json($place);
    }

    public function destroy(Place $place)
    {
        try {
            $place->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting place: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting place'], 500);
        }
    }
}