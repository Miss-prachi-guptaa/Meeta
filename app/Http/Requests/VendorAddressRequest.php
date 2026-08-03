<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:business,pickup,return',

            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'landmark'       => 'nullable|string|max:255',

            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'country'        => 'required|string|max:100',
            'postal_code'    => 'required|string|max:20',

            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ];
    }
}