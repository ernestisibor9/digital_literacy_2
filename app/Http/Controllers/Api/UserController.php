<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    /**
     * Display a listing of users.
     * Accessible to Admin only.
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200);
    }
    /**
     * Store a newly created user.
     * Accessible to Admin only.
     */
    public function store(Request $request)
    {

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,instructor,learner',
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user,
        ], 201);
    }
    /**
     * Change the role of a user.
     */
    public function changeUserRole(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'role' => 'required|in:admin,instructor,learner', // Allowed roles
        ]);

        // Find the user by ID
        $user = User::findOrFail($id);

        // Check if the role is being updated
        if ($user->role === $request->role) {
            return response()->json([
                'success' => false,
                'message' => 'The user already has the specified role.',
            ], 400);
        }

        // Update the user's role
        $user->update(['role' => $request->role]);

        return response()->json([
            'success' => true,
            'message' => "User role updated to '{$request->role}' successfully.",
            'data' => $user,
        ], 200);
    }
    /**
     * Update the specified user.
     * Accessible to Admin only.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|in:admin,instructor,learner',
        ]);

        try {
            $user->update([
                'firstname' => $request->firstname ?? $user->firstname,
                'lastname' => $request->lastname ?? $user->lastname,
                'email' => $request->email ?? $user->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
                'role' => $request->role ?? $user->role,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user.',
            ], 500);
        }
    }
    /**
     * Remove the specified user.
     * Accessible to Admin only.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ], 200);
    }
}
