<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVendorDocumentRequest extends FormRequest
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

        'document_type' => 'required|in:gst,pan,aadhaar,fssai,trade_license,cancelled_cheque',

        'document_number' => 'nullable|string|max:100',

        'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',

    ];
}
}
