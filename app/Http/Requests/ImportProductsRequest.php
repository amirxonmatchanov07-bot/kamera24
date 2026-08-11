<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.category' => ['required', 'string', 'max:100'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.cost' => ['required', 'numeric', 'min:0'],
            'items.*.icon' => ['nullable', 'string', 'max:50'],
            'items.*.image_base64' => ['nullable', 'string'],
        ];
    }
}
