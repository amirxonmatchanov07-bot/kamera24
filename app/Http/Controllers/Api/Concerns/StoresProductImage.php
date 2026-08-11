<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait StoresProductImage
{
    protected function storeProductImage(string $base64): ?string
    {
        $meta = '';

        if (str_contains($base64, ',')) {
            [$meta, $base64] = explode(',', $base64, 2);
        }

        $data = base64_decode($base64, true);

        if ($data === false || $data === '') {
            return null;
        }

        $extension = 'jpg';

        if (preg_match('/data:image\/(\w+);/', $meta, $matches)) {
            $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        }

        $path = 'products/'.Str::uuid().'.'.$extension;

        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
