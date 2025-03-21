<?php

namespace App\Http\Controllers;

use App\Models\ChallengeAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChallengeAnswerController extends Controller
{
    public function index($checkpoint_id)
    {
        try {
            $answers = ChallengeAnswer::where('checkpoint_id', $checkpoint_id)->get();
            return response()->json($answers);
        } catch (\Exception $e) {
            Log::error('Error fetching answers: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching answers'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'checkpoint_id' => 'required|exists:checkpoints,id',
                'answers' => 'required|array|min:2',
                'answers.*.answer' => 'required|string',
                'answers.*.is_correct' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Verificar que solo haya una respuesta correcta
            $correctAnswers = collect($request->answers)->where('is_correct', true)->count();
            if ($correctAnswers !== 1) {
                return response()->json(['error' => 'Debe haber exactamente una respuesta correcta'], 422);
            }

            // Eliminar respuestas anteriores si existen
            ChallengeAnswer::where('checkpoint_id', $request->checkpoint_id)->delete();

            // Crear las nuevas respuestas
            $answers = collect($request->answers)->map(function ($answer) use ($request) {
                return ChallengeAnswer::create([
                    'checkpoint_id' => $request->checkpoint_id,
                    'answer' => $answer['answer'],
                    'is_correct' => $answer['is_correct']
                ]);
            });

            Log::info('Answers created successfully for checkpoint: ' . $request->checkpoint_id);
            return response()->json($answers, 201);

        } catch (\Exception $e) {
            Log::error('Error creating answers: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating answers'], 500);
        }
    }

    public function verifyAnswer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'checkpoint_id' => 'required|exists:checkpoints,id',
                'answer_id' => 'required|exists:challenge_answers,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $answer = ChallengeAnswer::find($request->answer_id);
            
            if ($answer->checkpoint_id != $request->checkpoint_id) {
                return response()->json(['error' => 'La respuesta no corresponde a este punto de control'], 422);
            }

            return response()->json([
                'correct' => $answer->is_correct,
                'message' => $answer->is_correct ? '¡Correcto!' : 'Respuesta incorrecta, inténtalo de nuevo'
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying answer: ' . $e->getMessage());
            return response()->json(['error' => 'Error al verificar la respuesta'], 500);
        }
    }
} 