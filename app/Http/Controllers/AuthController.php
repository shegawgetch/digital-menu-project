<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request; // ✅ Needed for password hashing
use Illuminate\Support\Facades\Hash; // ✅ Model import

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'business_name' => 'required|string|max:255',
            'tin' => 'required|string|unique:users,tin',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'business_name' => $validated['business_name'],
            'tin' => $validated['tin'],
            'password' => Hash::make($validated['password']),
        ]);

        // Simulate credentials (for demo purpose)
        $credentials = [
            'email' => $validated['email'],
            'password' => $request->password, // keep original plain password only in response
        ];

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'credentials' => $credentials,
        ], 201);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create token with Laravel Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        // Delete the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
