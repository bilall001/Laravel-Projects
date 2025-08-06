<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function userRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $user = $this->userService->register($request->all());
        // $token = $user->createToken('API_Token')->plainTextToken;
        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => $user['user'],
            'token'    => $user['token'],
            // 'token' => $token,
        ]);
    }
    public function userLogin(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    try {
        $user = $this->userService->login($request->only('email', 'password'));

        return response()->json([
            'message' => 'User logged in successfully.',
            'token'   => $user['token'],
            'user'    => $user['user']
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Invalid credentials.'
        ], 401);
    }
}

}
