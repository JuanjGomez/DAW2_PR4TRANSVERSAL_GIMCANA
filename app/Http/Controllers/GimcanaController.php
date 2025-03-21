<?php

namespace App\Http\Controllers;

use App\Models\Gimcana;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
=======
use Illuminate\Support\Str;
use App\Models\Group;
use App\Models\Gimcana;
>>>>>>> fc898cb617f3cd6fdf8fb0a4d216429ddc9f3e90

class GimcanaController extends Controller
{
    public function index()
    {
        try {
            $gimcanas = Gimcana::all();
            return response()->json($gimcanas);
        } catch (\Exception $e) {
            Log::error('Error al obtener gimcanas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las gimcanas'], 500);
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
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $gimcana = Gimcana::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json($gimcana, 201);
        } catch (\Exception $e) {
            Log::error('Error al crear gimcana: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear la gimcana'], 500);
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
<<<<<<< HEAD
=======

    public function createGimcana(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'max_players' => 'required|integer|min:2',
            'num_groups' => 'required|integer|min:2'
        ], [
            'max_players.min' => 'Debe haber al menos 2 jugadores',
            'num_groups.min' => 'Debe haber al menos 2 grupos'
        ]);

        // Verificar que el número de jugadores sea suficiente para los grupos
        if ($request->max_players < $request->num_groups * 2) {
            return back()->withErrors([
                'max_players' => 'Debe haber al menos 2 jugadores por grupo'
            ])->withInput();
        }
    }

    public function joinGimcana(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $group = Group::where('code', $request->code)->first();

        if (!$group) {
            return back()->withErrors(['code' => 'Código inválido']);
        }

        // Verificar si hay espacio en el grupo
        if ($group->members()->count() >= $group->challenge->max_players) {
            return back()->withErrors(['code' => 'El grupo está lleno']);
        }

        // Unir al usuario al grupo
        // $group->members()->attach(auth()->id());

        return redirect()->route('user.dashboard')->with('success', 'Te has unido a la gimcana correctamente!');
    }

    public function getGimcanas()
    {
        $gimcanas = Gimcana::all();
        return response()->json($gimcanas);
    }
>>>>>>> fc898cb617f3cd6fdf8fb0a4d216429ddc9f3e90
}