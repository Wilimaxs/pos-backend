<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{

    protected $primaryKey = 'sku';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'category_code',
        'image_path',
        'barcode',
        'name',
        'unit',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            $product->sku = sprintf(
                '%s-%s',
                'SKU',
                Str::upper(Str::random(8))
            );
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_code');
    }

    public function productStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class, 'sku', 'sku');
    }
}
