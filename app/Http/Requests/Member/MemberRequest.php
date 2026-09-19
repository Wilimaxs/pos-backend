<?php

namespace App\Http\Requests\Member;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],

        ];
    }

    public function messages(): array
    {
        return [
            'search.string' => 'Kata pencarian harus berupa teks.',
            'search.max' => 'Kata pencarian maksimal 50 karakter.',
            'is_active.boolean' => 'Status aktif tidak valid.',
        ];
    }
}
