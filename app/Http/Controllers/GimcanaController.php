<?php

namespace App\Http\Controllers;

use App\Models\Gimcana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Group;

class GimcanaController extends Controller
{
    public function index()
    {
        try {
            $gimcanas = Gimcana::all();
            return response()->json($gimcanas);
        } catch (\Exception $e) {
            Log::error('Error fetching gimcanas: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching gimcanas'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed: ' . json_encode($validator->errors()));
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $gimcana = Gimcana::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            Log::info('Gimcana created successfully: ' . $gimcana->id);
            return response()->json($gimcana, 201);

        } catch (\Exception $e) {
            Log::error('Error creating gimcana: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating gimcana'], 500);
        }
    }

    public function show(Gimcana $gimcana)
    {
        try {
            return response()->json($gimcana->load('checkpoints.place'), 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            Log::error('Error al obtener gimcana: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener la gimcana'], 500);
        }
    }

    public function update(Request $request, Gimcana $gimcana)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $gimcana->update($request->all());
            return response()->json($gimcana, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            Log::error('Error al actualizar gimcana: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la gimcana'], 500);
        }
    }

    public function destroy(Gimcana $gimcana)
    {
        try {
            $gimcana->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error al eliminar gimcana: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la gimcana'], 500);
        }
    }

    public function showGimcanaForm()
    {
        return view('gimcana');
    }

    public function getGimcanas()
    {
        try {
            $gimcanas = Gimcana::with('groups.members')->get();
            $gimcanas->each(function ($gimcana) {
                $gimcana->current_groups = $gimcana->groups->count();
                $gimcana->current_players = $gimcana->groups->sum(function ($group) {
                    return $group->members->count();
                });
            });
            return response()->json($gimcanas);
        } catch (\Exception $e) {
            Log::error('Error al obtener gimcanas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las gimcanas'], 500);
        }
    }

    public function showGimcana($id)
    {
        try {
            $gimcana = Gimcana::with('groups.members')->findOrFail($id);
            return response()->json($gimcana);
        } catch (\Exception $e) {
            Log::error('Error al obtener detalles de la gimcana: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los detalles de la gimcana'], 500);
        }
    }
}