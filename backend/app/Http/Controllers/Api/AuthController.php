<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        // Validate incoming registration data
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
        ]);

        // Create user with hashed password
        $user = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Generate API token (Laravel Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return user and token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Login user using email OR username

    public function login(Request $request)
    {
        // Validate login input
        $data = $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ]);

        // Find user by email or username
        $user = User::where('email', $data['login'])
            ->orWhere('username', $data['login'])
            ->first();

        // Check password
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Nepareizs e-pasts vai parole.'],
            ]);
        }

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return authenticated user and token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Logout current user (invalidate current token)
    public function logout(Request $request)
    {
        // Delete current token only (not all sessions)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Izrakstīšanās veiksmīga.',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    // Update authenticated user's profile
    public function updateProfile(Request $request)
    {
        // Validate profile data
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'string'], // base64 image
        ]);

        // Update current user
        $user = $request->user();
        $data['avatar'] = ImageStorage::storeDataUrl(
            $data['avatar'] ?? null,
            'avatars',
            $request->getSchemeAndHttpHost(),
            $user->avatar
        );

        $user->update($data);

        return response()->json([
            'user' => $user,
        ]);
    }

    // Update authenticated user's password
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Pašreizējā parole nav pareiza.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'message' => 'Parole atjaunināta.',
        ]);
    }

    // Show another user's profile with their books
    public function showUser(User $user)
    {
        return response()->json([
            'user' => $user,
            'books' => $user->books()->latest()->get(),
        ]);
    }
}
