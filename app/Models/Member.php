<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Member extends Model
{
    protected $primaryKey = 'member_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Member $member) {
            $member->member_code = sprintf(
                '%s-%s',
                'MBR',
                Str::upper(Str::random(8))
            );
        });
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
