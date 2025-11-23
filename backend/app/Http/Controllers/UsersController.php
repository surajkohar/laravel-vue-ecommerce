<?php

namespace App\Http\Controllers;

use App\Libraries\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Mail\WelcomeEmail;
use App\Models\Admin\EmailTemplate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
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
            'phone' => 'required|numeric|digits_between:10,15',
            'status' => 'required|boolean',
            'roleid' => 'nullable|exists:roles,id',
            'password' => 'nullable|string|min:8',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'send_welcome_email' => 'nullable',
            'welcome_message' => 'nullable|string',
        ]);

        // Log::info($request->all());
        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        try {
            // Handle profile image upload
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
            }

            // Generate password if not provided
            $password = $request->password ?? Str::random(12);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
                'password' => bcrypt($password),
                'profile_image' => $profileImagePath,
            ]);

            // Assign role if provided
            if ($request->roleid) {
                $role = Role::where('id', $request->roleid)
                    ->where('guard_name', 'web')
                    ->firstOrFail();
                $user->assignRole($role->name);
            }

            // Send welcome email if requested
            if ($request->input('send_welcome_email')) {
                $this->sendWelcomeEmailUsingTemplate($user, $password, $request->welcome_message);
            }

            return $this->respondWithSuccess('User created successfully!', [
                'user' => $user,
                'generated_password' => $request->password ? null : $password,
            ], 201);
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            return $this->respondWithError('Failed to create user. Please try again later.', 500);
        }
    }


    protected function uploadProfileImage($file)
    {
        $filename = 'profile_' . time() . '.' . $file->getClientOriginalExtension();
        // Only save relative path (no 'public/' prefix)
        $file->storeAs('public/profile_images', $filename);
        return 'profile_images/' . $filename; // <-- Save this in DB
    }

    protected function respondWithSuccess($message, $data = [], $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function respondWithError($message, $status = 400, $errors = [])
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public function edit($id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);

            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'role_id' => $user->roles->first()?->id,
                'profile_image' => $user->profile_image
                    ? (str_starts_with($user->profile_image, 'storage/')
                        ? asset($user->profile_image)
                        : asset('storage/' . $user->profile_image))
                    : null,
            ];
            return $this->respondWithSuccess('User fetched successfully', $data);
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
            'password' => 'nullable|string|min:8',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_profile_image' => 'nullable|boolean',
            'send_update_email' => 'nullable',
            'update_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        try {
            $user = User::findOrFail($id);

            // Update basic info
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ]);

            // Update password if provided
            if ($request->password) {
                $user->password = bcrypt($request->password);
                $user->save();
            }
            // Log::info($request->all());
            // Handle profile image
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image) {
                    Storage::delete($user->profile_image);
                }

                // Store new image
                $path = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $path;
                $user->save();
            } elseif ($request->remove_profile_image) {
                // Remove existing profile image
                if ($user->profile_image) {
                    Storage::delete($user->profile_image);
                }
                $user->profile_image = null;
                $user->save();
            }

            // Update Role
            if ($request->roleid) {
                $role = Role::where('id', $request->roleid)
                    ->where('guard_name', 'web')
                    ->firstOrFail();
                $user->syncRoles([$role->name]);
            } else {
                $user->syncRoles([]);
            }

            // Send update email if requested and password was changed
            if ($request->send_update_email && $request->password) {
                $this->sendUpdateEmailUsingTemplate($user, $request->password, $request->update_message);
            }

            return $this->respondWithSuccess('User updated successfully!', [
                'user' => $user,
                'profile_image_url' => $user->profile_image
                    ? asset('storage/' . str_replace('public/', '', $user->profile_image))
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage());
            return $this->respondWithError('Failed to update user. Please try again later.', 500);
        }
    }

    protected function sendWelcomeEmailUsingTemplate($user, $password, $customMessage = null)
    {
        try {
            $shortCodes = [
                '{first_name}' => $user->name,
                '{last_name}' => '', // Add if you have last name
                '{email}' => $user->email,
                '{password}' => $password,
                '{admin_link}' => url('/admin'),
                '{company_name}' => config('app.name'),
                '{custom_message}' => $customMessage ?? ''
            ];

            General::sendTemplateEmail(
                $user->email,
                'admin-registration', // Using your existing template
                $shortCodes
            );
        } catch (\Exception $e) {
            Log::error('Welcome email failed to send: ' . $e->getMessage());
        }
    }

    protected function sendUpdateEmailUsingTemplate($user, $newPassword, $customMessage = null)
    {
        try {
            $shortCodes = [
                '{first_name}' => $user->name,
                '{last_name}' => '', // Add if you have last name
                '{email}' => $user->email,
                '{new_password}' => $newPassword,
                '{admin_link}' => url('/admin'),
                '{company_name}' => config('app.name'),
                '{custom_message}' => $customMessage ?? ''
            ];

            General::sendTemplateEmail(
                $user->email,
                'admin-credentials-update', // Using the new template
                $shortCodes
            );
        } catch (\Exception $e) {
            Log::error('Update email failed to send: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user(); // Logged-in user

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|numeric|digits_between:7,15',
            'password' => 'nullable|string|min:8',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_profile_image' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Update basic fields
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;

            // Update password if provided
            if ($request->password) {
                $user->password = bcrypt($request->password);
            }

            // Handle profile image
            if ($request->hasFile('profile_image')) {
                // Delete old
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                // Save new
                $path = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $path;
            }

            // Remove profile image
            if ($request->remove_profile_image) {
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $user->profile_image = null;
            }

            $user->save();

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user,
                'profile_image_url' => $user->profile_image
                    ? asset('storage/' . $user->profile_image)
                    : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
