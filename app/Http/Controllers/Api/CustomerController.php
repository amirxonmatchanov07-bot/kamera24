<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CustomerResource::collection(Customer::orderByDesc('debt')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'debt' => 0,
        ]);

        return response()->json([
            'message' => "Mijoz qo'shildi",
            'customer' => new CustomerResource($customer),
        ], 201);
    }

    public function settle(Request $request, Customer $customer): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $paid = min(round((float) $data['amount'], 2), (float) $customer->debt);

        $customer->update([
            'debt' => round((float) $customer->debt - $paid, 2),
            'last_payment_at' => now(),
        ]);

        return response()->json([
            'message' => $customer->debt > 0 ? "To'lov qabul qilindi" : "Qarz to'liq yopildi",
            'customer' => new CustomerResource($customer),
        ]);
    }
}
