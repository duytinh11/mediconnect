<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới (FR1)
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => 'patient',
            ]);

            $user->patient()->create([
                'address' => $data['address'],
                'gender' => $data['gender'] ?? null,
                'dob' => $data['dob'] ?? null,
            ]);

            return $user;
        });

        $token = $user->createToken('spa')->plainTextToken;

        return response()->json([
            'user' => $user->load('patient'),
            'token' => $token,
        ], 201);
    }

    /**
     * Đăng nhập (FR4)
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Account disabled'], 403);
        }

        $token = $user->createToken('spa')->plainTextToken;

        return response()->json([
            'user' => $user->load(['doctor', 'patient']),
            'token' => $token,
        ]);
    }

    /**
     * Đăng xuất (FR8)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
