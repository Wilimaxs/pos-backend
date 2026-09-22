<?php

namespace App\Http\Service\Auth;

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    public function login(array $credentials): array
    {
        $employee = Employee::query()
            ->with([
                'store:store_code,name',
                'employeePermissions.permission:id,name',
            ])
            ->where('phone', $credentials['phone'])
            ->first();

        if (!$employee || !$employee->is_active || !Hash::check($credentials['password'], $employee->password)) {
            throw new HttpException(401);
        }

        $employee->tokens()->delete();

        $token = $employee->createToken(
            name: 'mobile',
            expiresAt: now()->addHours(8),
        );

        return [
            'token' => $token->plainTextToken,
            'employee' => $employee,
        ];
    }
}
