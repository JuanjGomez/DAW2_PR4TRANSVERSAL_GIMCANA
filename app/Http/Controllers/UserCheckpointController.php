<?php

namespace App\Http\Controllers;

use App\Models\UserCheckpoints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserCheckpointController extends Controller
{
    public function store(Request $request)
    {
        $userCheckpoint = UserCheckpoints::create($request->all());
        return response()->json($userCheckpoint, 201);
    }

    public function getCompleted(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            $completedCheckpoints = UserCheckpoints::where('user_id', $userId)
                ->where('completed', true)
                ->get();

            return response()->json($completedCheckpoints);
        } catch (\Exception $e) {
            Log::error('Error getting completed checkpoints: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener checkpoints completados'], 500);
        }
    }
}