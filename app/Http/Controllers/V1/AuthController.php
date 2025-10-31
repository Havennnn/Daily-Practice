<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Http\Controllers\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\V1\AuthRegisterRequest;
use App\Http\Requests\V1\AuthLoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function register(AuthRegisterRequest $request) {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->createdResponse([
            'user' => UserResource::make($user)->resolve(),
            'token' => $token
        ], 'Registered Successfully');
    }

    public function login(AuthLoginRequest $request) {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if(! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('Invalid Credentials', null, 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => UserResource::make($user)->resolve(),
            'token' => $token
        ], 'Login Successfully', 200);
    }

    public function logout(Request $request) {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
            return $this->successResponse(null, 'Logout Successfully', 200);
        }

        return $this->errorResponse('No active token found', null, 401);
    }
}
