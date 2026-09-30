<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    public const CATEGORIES = [
        'T-Shirts',
        'Oversized Tees',
        'Graphic Tees',
        'Basics',
        'Other',
    ];

    protected $fillable = [
        'name',
        'description',
        'category',
        'image_path',
        'price',
        'stock_quantity',
        'status',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
