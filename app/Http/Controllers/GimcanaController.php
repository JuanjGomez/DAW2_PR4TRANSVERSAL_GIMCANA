<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Group;
use App\Models\Gimcana;

class GimcanaController extends Controller
{
    public function showGimcanaForm()
    {
        return view('gimcana');
    }

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
}