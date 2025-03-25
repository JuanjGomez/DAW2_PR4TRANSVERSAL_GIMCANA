<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\ChallengeAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CheckpointController extends Controller
{
    public function index()
    {
        try {
            $checkpoints = Checkpoint::with(['place', 'gimcana'])->get();
            
            // Para cada checkpoint, obtener sus respuestas
            foreach ($checkpoints as $checkpoint) {
                $answers = ChallengeAnswer::where('checkpoint_id', $checkpoint->id)->get();
                $checkpoint->answers = $answers->pluck('answer')->toArray();
                $checkpoint->correct_answer = $answers->search(function ($answer) {
                    return $answer->is_correct;
                });
            }
            
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
                'order' => 'required|integer|min:1',
                'answers' => 'required|array|min:1',
                'correct_answer' => 'required|integer|min:0'
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

            // Verificar que el índice de respuesta correcta sea válido
            if ($request->correct_answer >= count($request->answers)) {
                return response()->json(['error' => 'El índice de respuesta correcta no es válido'], 422);
            }

            // Crear el checkpoint
            $checkpoint = Checkpoint::create([
                'place_id' => $request->place_id,
                'gimcana_id' => $request->gimcana_id,
                'challenge' => $request->challenge,
                'clue' => $request->clue,
                'order' => $request->order
            ]);

            // Guardar las respuestas
            foreach ($request->answers as $index => $answer) {
                ChallengeAnswer::create([
                    'checkpoint_id' => $checkpoint->id,
                    'answer' => $answer,
                    'is_correct' => $index === $request->correct_answer
                ]);
            }

            // Cargar las relaciones necesarias
            $checkpoint->load(['place', 'gimcana']);
            
            // Añadir las respuestas a la respuesta JSON
            $checkpoint->answers = $request->answers;
            $checkpoint->correct_answer = $request->correct_answer;

            Log::info('Checkpoint created successfully: ' . $checkpoint->id);
            return response()->json($checkpoint, 201);
        } catch (\Exception $e) {
            Log::error('Error creating checkpoint: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el punto de control: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $checkpoint = Checkpoint::with(['place', 'gimcana'])->findOrFail($id);
            
            // Obtener las respuestas
            $answers = ChallengeAnswer::where('checkpoint_id', $id)->get();
            
            // Preparar las respuestas para la respuesta JSON
            $checkpoint->answers = $answers->pluck('answer')->toArray();
            $checkpoint->correct_answer = $answers->search(function ($answer) {
                return $answer->is_correct;
            });
            
            return response()->json($checkpoint);
        } catch (\ModelNotFoundException $e) {
            return response()->json(['error' => 'No se encontró el punto de control'], 404);
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

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'place_id' => 'required|exists:places,id',
                'gimcana_id' => 'required|exists:gimcanas,id',
                'challenge' => 'required|string',
                'clue' => 'required|string',
                'order' => 'required|integer|min:1',
                'answers' => 'required|array|min:1',
                'correct_answer' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Verificar que el checkpoint existe
            $checkpoint = Checkpoint::findOrFail($id);

            // Verificar que el índice de respuesta correcta sea válido
            if ($request->correct_answer >= count($request->answers)) {
                return response()->json(['error' => 'El índice de respuesta correcta no es válido'], 422);
            }

            // Actualizar el checkpoint
            $checkpoint->update([
                'place_id' => $request->place_id,
                'gimcana_id' => $request->gimcana_id,
                'challenge' => $request->challenge,
                'clue' => $request->clue,
                'order' => $request->order
            ]);

            // Eliminar las respuestas anteriores
            ChallengeAnswer::where('checkpoint_id', $checkpoint->id)->delete();

            // Guardar las nuevas respuestas
            foreach ($request->answers as $index => $answer) {
                ChallengeAnswer::create([
                    'checkpoint_id' => $checkpoint->id,
                    'answer' => $answer,
                    'is_correct' => $index === $request->correct_answer
                ]);
            }

            // Cargar las relaciones necesarias
            $checkpoint->load(['place', 'gimcana']);
            
            // Añadir las respuestas a la respuesta JSON
            $checkpoint->answers = $request->answers;
            $checkpoint->correct_answer = $request->correct_answer;

            Log::info('Checkpoint updated successfully: ' . $checkpoint->id);
            return response()->json($checkpoint, 200);
        } catch (\ModelNotFoundException $e) {
            Log::error('Checkpoint not found: ' . $id);
            return response()->json(['error' => 'No se encontró el punto de control'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating checkpoint: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el punto de control: ' . $e->getMessage()], 500);
        }
    }
} 