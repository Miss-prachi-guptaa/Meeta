<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

 public function rules(): array
{
    return [
        'first_name' => 'required|string|max:255',
        'last_name'  => 'nullable|string|max:255',
        'email'      => 'required|email|unique:users,email',
        'phone'      => 'required|string|max:20|unique:users,phone',
        'password'   => 'required|string|min:8|confirmed',
    ];
}
}