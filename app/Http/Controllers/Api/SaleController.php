<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function store(StoreSaleRequest $request): JsonResponse
    {
        $items = collect($request->validated('items'));
        $payType = $request->validated('pay_type');
        $customerId = $request->validated('customer_id');

        $sale = DB::transaction(function () use ($items, $payType, $customerId, $request) {
            $products = Product::whereIn('id', $items->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $unitPrice = fn ($line, $product) => array_key_exists('price', $line) && $line['price'] !== null
                ? round((float) $line['price'], 2)
                : (float) $product->price;

            $total = round($items->sum(fn ($line) => $unitPrice($line, $products[$line['product_id']]) * $line['qty']), 2);

            $sale = Sale::create([
                'customer_id' => $customerId,
                'user_id' => $request->user()->id,
                'pay_type' => $payType,
                'total' => $total,
            ]);

            foreach ($items as $line) {
                $product = $products[$line['product_id']];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'qty' => $line['qty'],
                    'unit_price' => $unitPrice($line, $product),
                    'unit_cost' => $product->cost,
                ]);
            }

            if ($payType === 'Nasiya') {
                Customer::whereKey($customerId)->increment('debt', $total);
            }

            return $sale;
        });

        $sale->load('items.product', 'customer', 'user');

        return response()->json([
            'sale' => $this->receiptData($sale),
            'message' => 'Sotuv yakunlandi',
        ], 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        $sale->load('items.product', 'customer', 'user');

        return response()->json(['sale' => $this->receiptData($sale)]);
    }

    private function receiptData(Sale $sale): array
    {
        return [
            'id' => $sale->id,
            'total' => (float) $sale->total,
            'pay_type' => $sale->pay_type,
            'created_at' => $sale->created_at->toIso8601String(),
            'cashier' => $sale->user->name,
            'customer' => $sale->customer?->name,
            'items' => $sale->items->map(fn ($item) => [
                'name' => $item->product->name,
                'qty' => $item->qty,
                'unit_price' => (float) $item->unit_price,
                'line_total' => round($item->qty * (float) $item->unit_price, 2),
            ]),
        ];
    }
}
