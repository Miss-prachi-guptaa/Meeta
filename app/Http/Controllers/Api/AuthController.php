<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordResetOtp;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
     public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.',
        ], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login successful.',
        'token' => $token,
        'user' => $user,
    ], 200);
}

public function forgotPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $otp = rand(100000, 999999);

    try {

        Mail::to($request->email)->send(new SendOtpMail($otp));

        PasswordResetOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.',
        ], 500);

    }
}

public function verifyOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'otp' => 'required|digits:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $otpRecord = PasswordResetOtp::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->first();

    if (!$otpRecord) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP.'
        ], 400);
    }

    if (Carbon::now()->gt($otpRecord->expires_at)) {

        $otpRecord->delete();

        return response()->json([
            'success' => false,
            'message' => 'OTP has expired.'
        ], 400);
    }

    $otpRecord->update([
    'is_verified' => true,
]);

    return response()->json([
        'success' => true,
        'message' => 'OTP verified successfully.'
    ], 200);
}

public function resetPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $otpRecord = PasswordResetOtp::where('email', $request->email)
        ->where('is_verified', true)
        ->first();

    if (!$otpRecord) {
        return response()->json([
            'success' => false,
            'message' => 'Please verify OTP first.',
        ], 400);
    }

    if (now()->gt($otpRecord->expires_at)) {

        $otpRecord->delete();

        return response()->json([
            'success' => false,
            'message' => 'OTP has expired.',
        ], 400);
    }

    $user = User::where('email', $request->email)->first();

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    $otpRecord->delete();

    return response()->json([
        'success' => true,
        'message' => 'Password reset successfully.',
    ], 200);
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully.',
    ], 200);
}
public function profile(Request $request)
{
    return response()->json([
        'success' => true,
        'user' => $request->user(),
    ]);
}

public function updateProfile(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:15',
        'email' => 'required|email|unique:users,email,' . $request->user()->id,
        'address' => 'nullable|string|max:255',
        'gender' => 'nullable|in:Male,Female,Other',
        'date_of_birth' => 'nullable|date',
        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    $user = $request->user();

    if ($request->hasFile('profile_image')) {

        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $imagePath = $request->file('profile_image')->store('profile_images', 'public');

        $user->profile_image = $imagePath;
    }

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
        'gender' => $request->gender,
        'date_of_birth' => $request->date_of_birth,
        'profile_image' => $user->profile_image,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'user' => $user->fresh(),
    ], 200);
}
}
