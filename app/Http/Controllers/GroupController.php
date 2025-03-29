<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\User;
use App\Models\UserCheckpoints;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    public function joinGroup(Request $request)
    {
        $user = Auth::user();
        $groupId = $request->input('group_id');

        $group = Group::with('members')->find($groupId);
        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Grupo no encontrado']);
        }

        // Verificar si el usuario ya está en el grupo
        if ($group->members->contains('id', $user->id)) {
            return response()->json(['success' => true, 'message' => 'Ya eres miembro de este grupo']);
        }

        // Verificar si el usuario ya está en cualquier grupo de cualquier gimcana
        $userGroups = Group::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        if ($userGroups->isNotEmpty()) {
            return response()->json(['success' => false, 'message' => 'Ya estás en un grupo de otra gimcana']);
        }

        // Agregar al usuario al grupo
        $group->members()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Te has unido al grupo',
            'group' => $group->load('members'),
            'user' => ['id' => $user->id]
        ]);
    }

    public function checkUserGroupStatus($gimcanaId)
    {
        $user = Auth::user();
        $userGroup = Group::whereHas('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('gimcana_id', $gimcanaId)->first();

        if ($userGroup) {
            return response()->json(['inGroup' => true, 'groupId' => $userGroup->id]);
        }

        return response()->json(['inGroup' => false]);
    }

    public function leaveGroup(Request $request)
    {
        $user = Auth::user();
        $groupId = $request->input('group_id');

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Grupo no encontrado']);
        }

        // Eliminar al usuario del grupo
        $group->members()->detach($user->id);

        return response()->json(['success' => true, 'message' => 'Has salido del grupo']);
    }

    public function show($id)
    {
        $group = Group::with('members')->find($id);
        if (!$group) {
            return response()->json(['error' => 'Grupo no encontrado'], 404);
        }
        return response()->json($group);
    }

    public function checkCheckpointProgress($groupId, $checkpointId)
    {
        try {
            $group = Group::with('members')->findOrFail($groupId);
            $totalMembers = $group->members->count();

            // Contar cuántos miembros diferentes han completado este checkpoint
            $completedCount = UserCheckpoints::where('group_id', $groupId)
                ->where('checkpoint_id', $checkpointId)
                ->where('completed', true)
                ->distinct('user_id')  // Asegurarse de contar cada usuario una sola vez
                ->count('user_id');    // Contar por user_id en lugar de todas las filas

            Log::info("Checkpoint Progress - Group: $groupId, Checkpoint: $checkpointId", [
                'totalMembers' => $totalMembers,
                'completedCount' => $completedCount
            ]);

            return response()->json([
                'allCompleted' => $completedCount >= $totalMembers,
                'completed' => $completedCount,
                'total' => $totalMembers
            ]);
        } catch (\Exception $e) {
            Log::error("Error checking checkpoint progress: " . $e->getMessage());
            return response()->json(['error' => 'Error al verificar el progreso'], 500);
        }
    }
}
