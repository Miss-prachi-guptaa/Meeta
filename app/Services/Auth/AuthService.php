<?php

namespace App\Services\Auth;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        $user = $this->authRepository->createUser($data);
         // Assign Customer Role
         $user->assignRole('customer');

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

public function login(array $data)
{
    $user = $this->authRepository->findUserByEmail($data['email']);

    if (!$user || !Hash::check($data['password'], $user->password)) {
        return [
            'success' => false,
            'message' => 'Invalid email or password.',
            'status' => 401,
        ];
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    $user->load('roles');
    $user->role = $user->getRoleNames()->first();

unset($user->roles);

    return [
        'success' => true,
        'message' => 'Login successful.',
        'token' => $token,
        'user' => $user,
        'status' => 200,
    ];
}


public function forgotPassword(array $data)
{
    $otp = rand(100000, 999999);

    try {

        Mail::to($data['email'])->send(new SendOtpMail($otp));

        $this->authRepository->createOrUpdateOtp(
            $data['email'],
            $otp
        );

        return [
            'success' => true,
            'message' => 'OTP sent successfully.',
            'status' => 200,
        ];

    } catch (\Exception $e) {

        return [
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.',
            'status' => 500,
        ];

    }
}

public function verifyOtp(array $data)
{
    $otpRecord = $this->authRepository->getOtpRecord(
        $data['email'],
        $data['otp']
    );

    if (!$otpRecord) {
        return [
            'success' => false,
            'message' => 'Invalid OTP.',
            'status' => 400,
        ];
    }

    if (Carbon::now()->gt($otpRecord->expires_at)) {

        $this->authRepository->deleteOtp($otpRecord);

        return [
            'success' => false,
            'message' => 'OTP has expired.',
            'status' => 400,
        ];
    }

    $this->authRepository->verifyOtp($otpRecord);

    return [
        'success' => true,
        'message' => 'OTP verified successfully.',
        'status' => 200,
    ];
}
public function resetPassword(array $data)
{
    $otpRecord = $this->authRepository->getVerifiedOtp($data['email']);

    if (!$otpRecord) {
        return [
            'success' => false,
            'message' => 'Please verify OTP first.',
            'status' => 400,
        ];
    }

    if (now()->gt($otpRecord->expires_at)) {

        $this->authRepository->deleteOtp($otpRecord);

        return [
            'success' => false,
            'message' => 'OTP has expired.',
            'status' => 400,
        ];
    }

    $user = $this->authRepository->findUserByEmail($data['email']);

    $this->authRepository->updatePassword(
        $user,
        Hash::make($data['password'])
    );

    $this->authRepository->deleteOtp($otpRecord);

    return [
        'success' => true,
        'message' => 'Password reset successfully.',
        'status' => 200,
    ];
}

public function logout($user)
{
    $user->currentAccessToken()->delete();

    return [
        'success' => true,
        'message' => 'Logged out successfully.',
        'status' => 200,
    ];
}

public function profile($user)
{
    return [
        'success' => true,
        'user' => $user,
    ];
}

public function updateProfile($request)
{
    $user = $request->user();

    $data = $request->validated();

    if ($request->hasFile('profile_image')) {

        if (
            $user->profile_image &&
            Storage::disk('public')->exists($user->profile_image)
        ) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $data['profile_image'] = $request
            ->file('profile_image')
            ->store('profile_images', 'public');
    }

    $user = $this->authRepository->updateProfile($user, $data);

    return [
        'success' => true,
        'message' => 'Profile updated successfully.',
        'user' => $user,
        'status' => 200,
    ];
}
}