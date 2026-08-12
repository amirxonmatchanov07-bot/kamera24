<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\StoresProductImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportProductsRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductImportController extends Controller
{
    use StoresProductImage;

    public function store(ImportProductsRequest $request): JsonResponse
    {
        $items = collect($request->validated('items'));

        [$created, $updated] = DB::transaction(function () use ($items) {
            $created = 0;
            $updated = 0;

            foreach ($items as $item) {
                $name = trim($item['name']);
                $price = round((float) $item['price'], 2);

                $attributes = [
                    'category' => trim($item['category']),
                    'price' => $price,
                ];

                if (isset($item['cost']) && $item['cost'] !== null) {
                    $attributes['cost'] = round((float) $item['cost'], 2);
                }

                if (! empty($item['image_base64'])) {
                    $attributes['image'] = $this->storeProductImage($item['image_base64']);
                }

                $product = Product::where('name', $name)->first();

                if ($product) {
                    $product->update($attributes);
                    $updated++;
                } else {
                    $attributes['name'] = $name;
                    $attributes['icon'] = $item['icon'] ?? 'package';
                    // Tannarx berilmagan bo'lsa, foyda hisobi 0 ko'rsatilishi uchun narxga tenglashtiramiz.
                    $attributes['cost'] = $attributes['cost'] ?? $price;
                    Product::create($attributes);
                    $created++;
                }
            }

            return [$created, $updated];
        });

        return response()->json([
            'message' => "{$created} ta yangi, {$updated} ta yangilangan mahsulot",
            'created' => $created,
            'updated' => $updated,
        ], 201);
    }
}
