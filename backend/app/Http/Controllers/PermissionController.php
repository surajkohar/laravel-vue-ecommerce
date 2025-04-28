<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionController extends ApiController
{
    public function index(Request $request)
    {
        try {
            // Log::info('Filters:', $request->query());

            $filters = $request->query();
            $query = Permission::query();

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        $permission = Permission::create([
            'name' => $request->input('name'),
            'guard_name' => 'web'
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Permission created successfully!',
            'permission' => $permission
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->input('name')
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Permission updated successfully!',
            'permission' => $permission
        ], 200);
    }

    public function edit($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['status' => false, 'message' => 'Permission not found'], 404);
        }
        return response()->json([
            'status' => true,
            'permission' => $permission
        ], 200);
    }

    public function assignPermission(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        try {
            $permission = Permission::find($request->id) ?: Permission::findByName($request->permission);

            if (!$permission) {
                return $this->respondWithError('Permission not found for guard `sanctum`.', 404);
            }

            $role->givePermissionTo($permission);

            return $this->respondWithSuccess('Permission assigned to role successfully!', $role->getAllPermissions(), 200);
        } catch (\Exception $e) {
            return $this->respondWithError('An error occurred: ' . $e->getMessage(), 500);
        }
    }

    // Remove permission from a role by name or ID
    public function removePermission(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        // Check if permission is provided by ID or name
        try {
            $permission = Permission::find($request->id) ?: Permission::findByName($request->permission);

            if (!$permission) {
                return $this->respondWithError('Permission not found for guard `sanctum`.', 404);
            }

            $role->revokePermissionTo($permission);

            return $this->respondWithSuccess('Permission removed from role successfully!', $role->getAllPermissions(), 200);
        } catch (\Exception $e) {
            return $this->respondWithError('An error occurred: ' . $e->getMessage(), 500);
        }
    }


    // List all permissions of a user
    public function listPermissions($roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = $role->getAllPermissions();

        return $this->respondWithSuccess('Permissions retrieved successfully!', $permissions, 200);
    }

    public function deletePermission($permissionId)
    {
        try {
            $permissionExists = \DB::table('permissions')->where('id', $permissionId)->exists();
            if (!$permissionExists) {
                return $this->respondWithError('Permission not found.', 404);
            }
            // Delete the permission directly from the database
            \DB::table('permissions')->where('id', $permissionId)->delete();

            return response()->json(['message' => 'Permission deleted successfully!'], 200);
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to delete permission. Please try again later.', 500);
        }
    }
}
