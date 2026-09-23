<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee): void {
            $employee->employee_code = sprintf(
                '%s-%s',
                'EMP',
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

    public function employeePermissions(): HasMany
    {
        return $this->hasMany(EmployeePermission::class, 'employee_code', 'employee_code');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_code', 'store_code');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class, // Model yang ingin diakses
            'employee_permissions', // tabel penghubung dari employee ke permission
            'employee_code', // foreign key dari employee_permission ke employee
            'permission_id', // foreign key dari employee_permission ke permission
            'employee_code', // key dari employee
            'id' // key dari permission
        )->withTimestamps();
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->is_owner) {
            return true;
        }

        return $this->employeePermissions()
            ->whereHas(
                'permission',
                fn($query) => $query->where(
                    'name',
                    $permissionName
                )
            )
            ->exists();
    }
}
