<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_owner_only',
    ];


    protected function casts(): array
    {
        return [
            'is_owner_only' => 'boolean',
        ];
    }
}
