<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{
    RegisterRequest, LoginRequest
};
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\{Hash, Auth};

class AuthController extends Controller
{
    /**
     * Handle registration of a new user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->only('name', 'email');
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        $token = $user->createToken('Register')->plainTextToken;

        return response()->json([
            'data' => [
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('Login')->plainTextToken;

        return response()->json([
            'data' => [
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Handle user logout.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'data' => [
                'message' => 'Logged out successfully'
            ]
        ]);
    }
}
