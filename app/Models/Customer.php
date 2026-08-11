<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'phone', 'debt', 'last_payment_at'])]
class Customer extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'debt' => 'decimal:2',
            'last_payment_at' => 'datetime',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
