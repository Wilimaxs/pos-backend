<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'permission_ids' => ['present', 'array'],
            'permission_ids.*' => ['integer', 'distinct', Rule::exists('permissions', 'id')]
        ];
    }

    public function messages(): array
    {
        return [
            'permission_ids.present' => 'Daftar permission wajib dikirim.',
            'permission_ids.array' => 'Daftar permission harus berupa array.',

            'permission_ids.*.integer' => 'ID permission harus berupa angka.',
            'permission_ids.*.distinct' => 'Permission tidak boleh duplikat.',
            'permission_ids.*.exists' => 'Permission tidak ditemukan.',
        ];
    }
}
