<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\PasswordResetOtp;


class AuthRepository
{
    public function createUser(array $data)
    {
        return User::create($data);
    }



public function findUserByEmail($email)
{
    return User::where('email', $email)->first();
}

public function createOrUpdateOtp(string $email, int $otp)
{
    return PasswordResetOtp::updateOrCreate(
        ['email' => $email],
        [
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]
    );
}

public function getOtpRecord(string $email, string $otp)
{
    return PasswordResetOtp::where('email', $email)
        ->where('otp', $otp)
        ->first();
}
public function verifyOtp($otpRecord)
{
    return $otpRecord->update([
        'is_verified' => true,
    ]);
}
public function deleteOtp($otpRecord)
{
    return $otpRecord->delete();
}

public function getVerifiedOtp(string $email)
{
    return PasswordResetOtp::where('email', $email)
        ->where('is_verified', true)
        ->first();
}

public function updatePassword($user, string $password)
{
    return $user->update([
        'password' => $password,
    ]);
}

public function updateProfile($user, array $data)
{
    $user->update($data);

    return $user->fresh();
}
}