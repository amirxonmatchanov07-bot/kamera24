<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'category', 'price', 'cost', 'icon', 'image'])]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
        ];
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $term ? $query->where('name', 'like', "%{$term}%") : $query;
    }
}
