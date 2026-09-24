<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Purchase extends Model
{
    protected $primaryKey = 'purchase_code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'supplier_receipt_number',
        'supplier_code',
        'store_code',
        'employee_code',
        'purchase_date',
        'payment_method',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Purchase $purchase): void {
            $purchase->purchase_code = sprintf('PUR-%s', Str::upper(Str::random(8)));
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_code', 'purchase_code');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_code', 'supplier_code');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_code', 'store_code');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_code', 'employee_code');
    }
}

