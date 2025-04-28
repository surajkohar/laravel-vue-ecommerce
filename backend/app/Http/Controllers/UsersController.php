<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersController extends ApiController
{
    public function index(Request $request)
    {
        try {
            // Log::info('Filters:', $request->query());

            $filters = $request->query();
            $query = User::query();

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|numeric|digits_between:10,15', // better validation for phone number
            'status' => 'required|boolean', // status required
            'roleid' => 'nullable|exists:roles,id', // check if role exists
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ]);

            // Assign role if roleid is provided
            if ($request->roleid) {
                $role = Role::findById($request->roleid, 'web'); // fix here
                $user->assignRole($role->name); // fix here
            }

            return $this->respondWithSuccess('User registered successfully!', $user, 201);
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            return $this->respondWithError('Failed to register user. Please try again later.', 500);
        }
    }

    public function edit($id)
    {
        try {
            $user = User::with('roles')->findOrFail($id); // load user with roles relationship

            return $this->respondWithSuccess('User fetched successfully', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'role_id' => $user->roles->first()?->id, // if user has a role, return its id, else null
            ]);
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to fetch user data.', 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|numeric|digits_between:10,15',
            'status' => 'required|boolean',
            'roleid' => 'nullable|exists:roles,id',
        ]);
        Log::info('Request data:', $request->all());

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        try {
            $user = User::findOrFail($id);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ]);

            // Update Role
            if ($request->roleid) {
                $role = Role::findById($request->roleid, 'web');
                $user->syncRoles([$role->name]);
            } else {
                $user->syncRoles([]); // remove all roles if roleid is null
            }

            return $this->respondWithSuccess('User updated successfully!', $user);
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to update user. Please try again later.', 500);
        }
    }
}
