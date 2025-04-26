<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class RoleController extends ApiController
{
    public function index(Request $request)
    {
        try {
            // Log::info('Filters:', $request->query());

            $filters = $request->query();
            $query = Role::query();

            // Apply search filter
            if (isset($filters['search']) && !empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('id', 'like', '%' . $filters['search'] . '%');
                });
            }

            // Apply date range filter
            if (isset($filters['createdFrom']) && !empty($filters['createdFrom'])) {
                $query->where('created_at', '>=', $filters['createdFrom']);
            }

            if (isset($filters['createdTo']) && !empty($filters['createdTo'])) {
                $query->where('created_at', '<=', $filters['createdTo'] . ' 23:59:59');
            }

            // Apply sorting
            $sortField = $filters['sort_field'] ?? 'id';
            $sortDirection = $filters['sort_direction'] ?? 'asc';
            $query->orderBy($sortField, $sortDirection);

            // Paginate results
            $perPage = $filters['per_page'] ?? 10;
            $roles = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'data' => $roles->items(),
                'meta' => [
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                    'per_page' => $roles->perPage(),
                    'total' => $roles->total(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch roles. Please try again later.'
            ], 500);
        }
    }


    public function create(Request $request)
    {
        Log::info('request:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
            'status' => 'required',
        ],[
            'permissions.required' => 'Please select at least one permission.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'sanctum',
            'status' => $request->input('status'),
        ]);

        // Assign permissions
        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }

        return response()->json([
            'status' => true,
            'message' => 'Role created successfully!',
            'role' => $role
        ], 201);
    }


    public function update(Request $request, $id)
    {
        Log::info('request:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
            'status' => 'required|in:0,1', 
        ], [
            'permissions.required' => 'Permission is required, select at least one.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->input('name'),
            'status' => $request->input('status'),
        ]);

        // Update permissions
        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }

        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully!',
            'role' => $role
        ], 200);
    }


    public function edit($id)
    {
        $role = Role::with('permissions:id,name')->find($id);

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'status' => $role->status,
                'permissions' => $role->permissions->pluck('id') // Just get permission IDs
            ]
        ], 200);
    }

    public function deleteRole($roleId)
    {
        try {

            $roleExists = DB::table('roles')->where('id', $roleId)->exists();
            if (!$roleExists) {
                return $this->respondWithError('Role not found.', 404);
            }
            DB::table('roles')->where('id', $roleId)->delete();
            return response()->json(['message' => 'Role deleted successfully!'], 200);
        } catch (\Exception $e) {
            Log::error("Failed to delete role: " . $e->getMessage());
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

    function fetchRolePermission()
    {
        $permissions = Permission::where('status',1)->get();
        $roles = Role::where('status',1)->get();
        return response()->json([
            "status" => true,
            'permissions' => $permissions,
            'roles' => $roles
        ], 200);
    }
}
