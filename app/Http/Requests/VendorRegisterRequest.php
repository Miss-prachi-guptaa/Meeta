<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required|string|unique:users,phone',
            'password'      => 'required|string|min:8|confirmed',

            'store_name'    => 'required|string|max:255',
            'business_name' => 'required|string|max:255',

            'gst_number'    => 'nullable|string|max:50',
            'pan_number'    => 'nullable|string|max:20',

            'description'   => 'nullable|string',
            'support_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:20',
        ];
    }
}