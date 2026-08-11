<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'pay_type' => ['required', Rule::in(['Naqd', 'Karta', 'Nasiya'])],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id', 'required_if:pay_type,Nasiya'],
        ];
    }
}
