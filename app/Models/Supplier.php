<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Supplier extends Model
{

    protected $primaryKey = 'supplier_code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'person_responsible',
        'phone',
        'address',
        'email',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Supplier $supplier) {
            $supplier->supplier_code = sprintf(
                '%s-%s',
                'SPL',
                Str::upper(Str::random(8))
            );
        });
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
