<?php

namespace App\Http\Requests\Vendor\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
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

            'barcode' => [
                'nullable',
                'string',
                'max:100',
            ],

            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'sale_price' => [
                'nullable',
                'numeric',
                'lte:price',
            ],

            'weight' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'length' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'width' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'height' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }
}