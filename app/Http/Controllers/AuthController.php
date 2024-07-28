<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $accessToken = $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value],
            now()->addMinutes((int) config('sanctum.expiration'))
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            now()->addMinutes((int) config('sanctum.refresh_expiration'))
        );

        return response()->json([
            'user' => $user,
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        $accessToken = $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value],
            now()->addMinutes((int) config('sanctum.expiration'))
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            now()->addMinutes((int) config('sanctum.refresh_expiration'))
        );

        return response()->json([
            'user' => $user,
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ], 200);
    }

    public function refreshToken()
    {
        auth()->user()->tokens()->delete();

        $accessToken = auth()->user()->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value],
            now()->addMinutes((int) config('sanctum.expiration'))
        );

        $refreshToken = auth()->user()->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            now()->addMinutes((int) config('sanctum.refresh_expiration'))
        );

        return response()->json([
            'user' => auth()->user(),
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
        ], 200);
    }
}
