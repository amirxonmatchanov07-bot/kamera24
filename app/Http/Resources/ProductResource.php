<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'price' => (float) $this->price,
            'icon' => $this->icon,
            'image_url' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'cost' => $request->user()?->isAdmin() ? (float) $this->cost : null,
        ];
    }
}
