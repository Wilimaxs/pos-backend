<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $table = 'employees';

    protected $primaryKey = 'employee_code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'store_code',
        'name',
        'email',
        'phone',
        'password',
        'address',
        'position',
        'is_owner',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee): void {
            $employee->employee_code = sprintf(
                '%s-%s',
                'EMP-',
                Str::upper(Str::random(8))
            );
        });
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_owner' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
