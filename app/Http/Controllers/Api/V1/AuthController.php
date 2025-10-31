<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AuthRegisterRequest;
use App\Http\Requests\V1\AuthLoginRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $request) {
        $validate = $request->validated();

        User::create([
            'name' => $validate['name'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registered Successfully'
        ], 201);
    }

    public function login(AuthLoginRequest $request) {
        $validate = $request->validated();

        $user = User::where('email', $validate['email'])->first();

        if(! $user || ! Hash::check($validate['password'], $user->password)) {
            return response()->json([
            'success' => false,
            'message' => 'Incorrect Credentials'
        ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Successfully',
            'token' => $token
        ], 200);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Successfully',
        ], 200);
    }
}
