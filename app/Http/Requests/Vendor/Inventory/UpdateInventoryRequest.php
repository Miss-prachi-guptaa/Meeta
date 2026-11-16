<?php

namespace App\Http\Requests\Vendor\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
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
            'quantity' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'reserved_quantity' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'minimum_stock' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'maximum_stock' => [
                'nullable',
                'integer',
                'gte:minimum_stock',
            ],
        ];
    }
}