<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\User;

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

        return response()->json(['success' => true, 'message' => 'Te has unido al grupo']);
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
}
