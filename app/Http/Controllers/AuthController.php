<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    // Register a new user
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully!'], 201);
    }

    // Login user
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            /** @var User|null $user */
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Logout user
    public function logout(): JsonResponse
    {
        /** @var User|null $user */
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully!'], 200);
    }
}
