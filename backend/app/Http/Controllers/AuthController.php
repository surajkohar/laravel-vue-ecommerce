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

class AuthController extends ApiController
{
    /**
     * Sign up a new user
     */
    public function signup(Request $request)
    {

        Log::info('Request data:', $request->all());

        // return return response()->json($data, 200);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);


            return $this->respondWithSuccess('User registered successfully!', $user, 201);
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to register user. Please try again later.', 500);
        }
    }

    /**
     * Log in a user and generate an API token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        // Use the 'web' guard for authentication
        if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
            return $this->respondWithError('Invalid login credentials', 401);
        }

        try {
            $user = Auth::user();
            // Create a token using Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();

            $user->profile_image =$user->profile_image ? asset('storage/' . $user->profile_image) : null;
            return $this->respondWithSuccess('Login successful', [
                'token' => $token,
                'user' => $user,
                'permissions' => $permissions,
            ], 200);
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to generate token. Please try again later.', 500);
        }
    }


    /**
     * Log out a user by deleting their token
     */
    public function logout(Request $request)
    {
        try {
            // Delete the current access token
            $request->user()->currentAccessToken()->delete();

            return $this->respondWithSuccess('Logged out successfully!', [], 200);
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to log out. Please try again later.', 500);
        }
    }

    /**
     * Send a password reset link to the user's email
     */
        public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        Log::info('Forgot password request for email: ' . $request->email);

        // Ensure user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->respondWithError('User not found', 404);
        }

        // Create a token
        $token = Str::random(60);

        // Store the token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Send the email
        Mail::to($request->email)->send(new ResetPasswordMail($token));

        return $this->respondWithSuccess('Reset link sent to your email.', null, 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        // Find the user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->respondWithError('User not found.', 404);
        }

        // Check if the token exists
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return $this->respondWithError('Invalid or expired token.', 400);
        }

        // Update the password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return $this->respondWithSuccess('Password reset successfully.', null, 200);
    }


}
