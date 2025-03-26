<?php

namespace App\Http\Controllers;

use App\Models\Gimcana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
                'description' => 'required|string',
                'max_groups' => 'required|integer|min:1',
                'max_users_per_group' => 'required|integer|min:1'
            ]);

        $gimcana = Gimcana::create([
            'name' => $request->name,
            'description' => $request->description,
            'max_groups' => $request->max_groups,
            'max_users_per_group' => $request->max_users_per_group,
            'status' => 'waiting' // Por defecto, la gimcana estará en estado 'waiting'
        ]);

            $gimcana = Gimcana::create([
                'name' => $request->name,
                'description' => $request->description,
                'max_groups' => $request->max_groups,
                'max_users_per_group' => $request->max_users_per_group
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
        return response()->json($gimcana);
    }

    public function update(Request $request, Gimcana $gimcana)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'max_groups' => 'sometimes|integer|min:1',
                'max_users_per_group' => 'sometimes|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $gimcana->update($request->all());
            return response()->json($gimcana, 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar gimcana: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la gimcana'], 500);
        }
    }

    public function destroy(Gimcana $gimcana)
    {
        try {
            $gimcana->delete();
            return response()->json(['message' => 'Gimcana eliminada correctamente'], 200);
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
            $gimcanas = Gimcana::all();
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

    public function checkIfGimcanaReady($id)
    {
        $gimcana = Gimcana::with('groups.members')->findOrFail($id);

        $allGroupsFull = $gimcana->groups->every(function ($group) use ($gimcana) {
            return $group->members->count() >= $gimcana->max_users_per_group;
        });

        if ($allGroupsFull) {
            // Comenzar partida
            $gimcana->status = 'active';
            $gimcana->save();
            return response()->json(['ready' => true]);
        }

        return response()->json(['ready' => false]);
    }
}
