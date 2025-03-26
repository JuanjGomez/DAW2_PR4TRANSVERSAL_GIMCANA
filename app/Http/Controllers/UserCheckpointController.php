<?php

namespace App\Http\Controllers;

use App\Models\UserCheckpoints;
use Illuminate\Http\Request;

class UserCheckpointController extends Controller
{
    public function store(Request $request)
    {
        $userCheckpoint = UserCheckpoints::create($request->all());
        return response()->json($userCheckpoint, 201);
    }
}