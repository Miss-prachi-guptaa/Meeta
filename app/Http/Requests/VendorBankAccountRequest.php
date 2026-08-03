<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_holder_name' => 'required|string|max:255',
            'bank_name'           => 'required|string|max:255',
            'branch_name'         => 'nullable|string|max:255',
            'account_number'      => 'required|string|max:30',
            'ifsc_code'           => 'required|string|max:20',
            'upi_id'              => 'nullable|string|max:255',
            'account_type'        => 'required|in:savings,current',
            'is_primary'          => 'nullable|boolean',
        ];
    }
}