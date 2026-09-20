<?php

namespace App\Http\Requests\Store;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateStoreRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (!$this->hasAny([
                    'name',
                    'email',
                    'phone',
                    'address',
                    'is_active',
                ])) {
                    $validator->errors()->add('data', 'Minimal satu data harus diperbarui.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Store wajib diisi.',
            'name.string' => 'Nama Store harus berupa teks.',
            'name.max' => 'Nama Store maksimal 255 karakter.',

            'email.required' => 'Email Store wajib diisi.',
            'email.email' => 'Format email Store tidak valid.',
            'email.max' => 'Email Store maksimal 255 karakter.',

            'phone.required' => 'Nomor telepon Store wajib diisi.',
            'phone.string' => 'Nomor telepon Store harus berupa teks.',
            'phone.max' => 'Nomor telepon Store maksimal 20 karakter.',

            'address.required' => 'Alamat Store wajib diisi.',
            'address.string' => 'Alamat Store harus berupa teks.',
            'address.max' => 'Alamat Store maksimal 255 karakter.',

            'is_active.boolean' => 'Status aktif tidak valid.',
        ];
    }
}
