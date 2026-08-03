<?php

namespace App\Services;

use App\Repositories\VendorRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorService
{
    public function __construct(
        protected VendorRepository $vendorRepository
    ) {}

    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {

            $user = $this->vendorRepository->createUser([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'],
                'password'   => Hash::make($data['password']),
                'status'     => 'active',
            ]);

            $vendor = $this->vendorRepository->createVendor([
                'user_id'        => $user->id,
                'store_name'     => $data['store_name'],
                'store_slug' => Str::slug($data['store_name']),
                'business_name'  => $data['business_name'],
                'gst_number'     => $data['gst_number'] ?? null,
                'pan_number'     => $data['pan_number'] ?? null,
                'description'    => $data['description'] ?? null,
                'support_email'  => $data['support_email'] ?? null,
                'support_phone'  => $data['support_phone'] ?? null,
                'status'         => 'draft',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'token'  => $token,
                'user'   => $user,
                'vendor' => $vendor,
            ];
        });
    }
}