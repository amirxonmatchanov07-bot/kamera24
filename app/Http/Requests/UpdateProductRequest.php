<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:100'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'cost' => ['sometimes', 'numeric', 'min:0'],
            'icon' => ['sometimes', 'string', 'max:50'],
            'image_base64' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
