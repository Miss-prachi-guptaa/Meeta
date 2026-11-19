<?php

namespace App\Http\Requests\Vendor\ProductImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
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
            'images' => [
                'required',
                'array',
                'min:1',
            ],

            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'alt_text' => [
                'nullable',
                'string',
                'max:255',
            ],

            'product_variant_id' => [
                'nullable',
                'integer',
                'exists:product_variants,id',
            ],
        ];
    }
}