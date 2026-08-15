<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\StoresProductImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use StoresProductImage;

    public function index(Request $request): AnonymousResourceCollection
    {
        $category = $request->string('category')->toString();

        $products = Product::query()
            ->search($request->string('search')->toString() ?: null)
            ->when($category !== '' && $category !== 'Barchasi', fn ($q) => $q->where('category', $category))
            ->orderBy('name')
            ->get();

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $data = $request->validated();

        if (! empty($data['image_base64'])) {
            $data['image'] = $this->storeProductImage($data['image_base64']);
        }
        unset($data['image_base64']);

        $product = Product::create($data);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $data = $request->validated();

        if (! empty($data['image_base64'])) {
            $data['image'] = $this->storeProductImage($data['image_base64']);
        }
        unset($data['image_base64']);

        $product->update($data);

        return new ProductResource($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Mahsulot o\'chirildi']);
    }

    public function bulkPriceUpdate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'percent' => ['required', 'numeric', 'min:-100', 'max:1000'],
        ]);

        $multiplier = 1 + ((float) $data['percent'] / 100);
        $count = Product::count();

        DB::table('products')->update([
            'price' => DB::raw('ROUND(price * ' . sprintf('%.10F', $multiplier) . ', 2)'),
        ]);

        return response()->json([
            'message' => "{$count} ta mahsulot narxi yangilandi",
        ]);
    }
}
