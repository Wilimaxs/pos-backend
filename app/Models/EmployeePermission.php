<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePermission extends Model
{
    protected $fillable = [
        'employee_code',
        'permission_id',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_code', 'employee_code');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }
}
