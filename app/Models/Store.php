<?php

namespace App\Models;

use App\Enum\StoreType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{

    protected $primaryKey = 'store_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'type',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Store $store) {
            $store->store_code = sprintf(
                '%s-%s',
                $store->type->codeStore(),
                Str::upper(Str::random(8))
            );
        });
    }

    protected $casts = [
        'type' => StoreType::class,
        'is_active' => 'boolean',
    ];
}
