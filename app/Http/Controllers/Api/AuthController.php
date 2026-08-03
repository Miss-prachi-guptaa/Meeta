<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;

class AuthController extends Controller
{

protected $authService;
public function __construct(AuthService $authService)
{
    $this->authService = $authService;
}

 public function register(RegisterRequest $request)
{
    try {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'token' => $result['token'],
            'user' => $result['user'],
        ], 201);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => 'Registration failed.',
            'error' => config('app.debug')
                ? $e->getMessage()
                : 'Internal Server Error'
        ], 500);
    }
}

 public function login(LoginRequest $request)
{
    $response = $this->authService->login($request->validated());

    return response()->json([
        'success' => $response['success'],
        'message' => $response['message'],
        'token' => $response['token'] ?? null,
        'user' => $response['user'] ?? null,
    ], $response['status']);
}

public function forgotPassword(ForgotPasswordRequest $request)
{
    $response = $this->authService->forgotPassword(
        $request->validated()
    );

    return response()->json([
        'success' => $response['success'],
        'message' => $response['message'],
    ], $response['status']);
}

public function verifyOtp(VerifyOtpRequest $request)
{
    $response = $this->authService->verifyOtp(
        $request->validated()
    );

    return response()->json([
        'success' => $response['success'],
        'message' => $response['message'],
    ], $response['status']);
}


public function resetPassword(ResetPasswordRequest $request)
{
    $response = $this->authService->resetPassword(
        $request->validated()
    );

    return response()->json([
        'success' => $response['success'],
        'message' => $response['message'],
    ], $response['status']);
}

public function logout(Request $request)
{
    $response = $this->authService->logout($request->user());

    return response()->json([
        'success' => $response['success'],
        'message' => $response['message'],
    ], $response['status']);
}
public function profile(Request $request)
{
    $response = $this->authService->profile($request->user());

    return response()->json([
        'success' => $response['success'],
        'user' => $response['user'],
    ]);
}

public function updateProfile(UpdateProfileRequest $request)
{
    $response = $this->authService->updateProfile($request);

    return response()->json([
        'success' => $response['success'],
        'message' => $response['message'],
        'user' => $response['user'],
    ], $response['status']);
}
}
