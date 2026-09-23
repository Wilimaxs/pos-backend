<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{

    protected $fillable = [
        'sku',
        'store_code',
        'stock_minimum',
        'stock_quantity',
        'selling_price'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_code', 'store_code');
    }
}
