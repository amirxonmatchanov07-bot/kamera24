<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:50'],
            'image_base64' => ['nullable', 'string'],
        ];
    }
}
