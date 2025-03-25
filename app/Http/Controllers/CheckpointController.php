<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckpointController extends Controller
{
    public function index()
    {
        try {
            $checkpoints = Checkpoint::with(['place', 'gimcana'])->get();
            return response()->json($checkpoints);
        } catch (\Exception $e) {
            Log::error('Error fetching checkpoints: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching checkpoints'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'place_id' => 'required|exists:places,id',
                'gimcana_id' => 'required|exists:gimcanas,id',
                'challenge' => 'required|string',
                'clue' => 'required|string',
                'order' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Verificar que no haya más de 4 checkpoints para esta gimcana
            $checkpointCount = Checkpoint::where('gimcana_id', $request->gimcana_id)->count();
            if ($checkpointCount >= 4) {
                return response()->json(['error' => 'Esta gimcana ya tiene el máximo de 4 puntos de control'], 422);
            }

            $checkpoint = Checkpoint::create($request->all());
            Log::info('Checkpoint created successfully: ' . $checkpoint->id);
            return response()->json($checkpoint->load(['place', 'gimcana']), 201);
        } catch (\Exception $e) {
            Log::error('Error creating checkpoint: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el punto de control'], 500);
        }
    }

    public function show(Checkpoint $checkpoint)
    {
        try {
            return response()->json($checkpoint->load(['place', 'gimcana']));
        } catch (\Exception $e) {
            Log::error('Error fetching checkpoint: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching checkpoint'], 500);
        }
    }

    public function destroy(Checkpoint $checkpoint)
    {
        try {
            $checkpoint->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting checkpoint: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting checkpoint'], 500);
        }
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        try {
            $validator = Validator::make($request->all(), [
                'place_id' => 'required|exists:places,id',
                'gimcana_id' => 'required|exists:gimcanas,id',
                'challenge' => 'required|string',
                'clue' => 'required|string',
                'order' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $checkpoint->update($request->all());
            return response()->json($checkpoint->load(['place', 'gimcana']), 200);
        } catch (\Exception $e) {
            Log::error('Error updating checkpoint: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el punto de control: ' . $e->getMessage()], 500);
        }
    }
} 