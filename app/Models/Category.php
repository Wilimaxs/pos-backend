<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{

    protected $primaryKey = 'category_code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'category_code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category): void {
            $category->category_code = sprintf(
                '%s-%s',
                'CAT',
                Str::upper(Str::random(8))
            );
        });
    }
}
