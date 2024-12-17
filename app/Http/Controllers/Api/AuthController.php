<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    // Register user
    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,instructor,learner',
        ]);
        try {
            $user = User::create([
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while registering the user.'], 500);
        }
    }
    // Login user
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while registering the user.'], 500);
        }
    }
    // Logout user
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logout successful']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while logging out the user.'], 500);
        }
    }
    //
    public function forgotPassword(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email', // Check if the email exists in the DB
        ]);

        // Send password reset link via email
        $status = Password::sendResetLink(
            $validated
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent successfully.'], 200);
        }

        return response()->json(['message' => 'Failed to send password reset link.'], 400);
    }
    //
    // Handle the reset password process
    public function resetPassword(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed', // Password confirmation
        ]);

        // Attempt to reset the password
        $status = Password::reset(
            $validated,
            function ($user) use ($validated) {
                // Set the new password
                $user->password = Hash::make($validated['password']);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully.'], 200);
        }

        return response()->json(['message' => 'Failed to reset password.'], 400);
    }
}
