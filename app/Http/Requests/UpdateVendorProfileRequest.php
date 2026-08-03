<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'store_name' => 'sometimes|string|max:255',
        'business_name' => 'sometimes|string|max:255',
        'gst_number' => 'nullable|string|max:50',
        'pan_number' => 'nullable|string|max:20',
        'description' => 'nullable|string',
        'support_email' => 'nullable|email',
        'support_phone' => 'nullable|string|max:20',
    ];
}
}
