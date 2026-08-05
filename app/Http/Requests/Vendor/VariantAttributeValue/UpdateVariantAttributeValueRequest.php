<?php

namespace App\Http\Requests\Vendor\VariantAttributeValue;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVariantAttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [
            'attribute_value_id' => [
                'required',
                'exists:attribute_values,id',
            ],
        ];
    }
}