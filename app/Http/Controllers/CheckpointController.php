<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    public function index()
    {
        $checkpoints = Checkpoint::orderBy('order')->get();
        return response()->json($checkpoints);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'hint' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'order' => 'required|integer|min:1',
            'points' => 'required|integer|min:0'
        ]);

        $checkpoint = Checkpoint::create($request->all());
        return response()->json($checkpoint, 201);
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'hint' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'order' => 'integer|min:1',
            'points' => 'integer|min:0'
        ]);

        $checkpoint->update($request->all());
        return response()->json($checkpoint);
    }

    public function destroy(Checkpoint $checkpoint)
    {
        $checkpoint->delete();
        return response()->json(['message' => 'Checkpoint eliminado correctamente']);
    }
}
