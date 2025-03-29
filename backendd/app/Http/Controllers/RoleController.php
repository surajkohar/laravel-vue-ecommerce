<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends ApiController
{
    public function index()
    {
        $roles = Role::all();
        return response()->json(
            [
                "status" > true,
                'roles' => $roles
            ],
            200
        );
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'web'
        ]);

        return response()->json(
            [
                'status' => true,
                'message' => 'Role created successfully!',
                'role' => $role
            ],
            201
        );
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $id,
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->input('name')
        ]);

        return response()->json(['status' => true, 'message' => 'Role updated successfully!', 'role' => $role], 200);
    }

    public function edit($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found'], 404);
        }
        return response()->json(['status' => true, 'role' => $role], 200);
    }

    public function deleteRole($roleId)
    {
        try {

            $roleExists = \DB::table('roles')->where('id', $roleId)->exists();
            if (!$roleExists) {
                return $this->respondWithError('Role not found.', 404);
            }
            \DB::table('roles')->where('id', $roleId)->delete();
            return response()->json(['message' => 'Role deleted successfully!'], 200);
        } catch (\Exception $e) {
            \Log::error("Failed to delete role: " . $e->getMessage());
            return $this->respondWithError('Failed to delete role. Please try again later.', 500);
        }
    }

    public function assignRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Validate that the request contains the role ID
        $request->validate([
            'id' => 'required|integer|exists:roles,id',
        ]);

        // Find the role by ID and ensure it uses the 'sanctum' guard
        $role = Role::find($request->id) ?: Role::findByName($request->role);
        if (!$role) {
            return response()->json(['status' => 'error', 'message' => 'Role not found for the sanctum guard.'], 404);
        }
        try {
            // Assign the role using the role's name
            $user->assignRole($role->name);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error assigning role: ' . $e->getMessage()], 500);
        }

        return response()->json(['status' => true, 'message' => 'Role assigned successfully!', 'roles' => $user->getRoleNames()], 200);
    }





    // Remove role from user
    public function removeRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // Check if the role exists for the current guard
        $role = Role::find($request->id) ?: Role::findByName($request->role);
        if (!$role) {
            return $this->respondWithError('Role not found for the sanctum guard.', 404);
        }

        $user->removeRole($role);

        return $this->respondWithSuccess('Role removed successfully!', $user->getRoleNames(), 200);
    }

    // List all roles of a user
    public function listRoles($userId)
    {
        $user = User::findOrFail($userId);
        $roles = $user->getRoleNames(); // Returns a collection of role names
        return $this->respondWithSuccess('Roles retrieved successfully!', $roles, 200);
    }
}
