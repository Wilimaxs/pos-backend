<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSupplierRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'person_responsible' => ['nullable', 'string', 'max:255',],
            'phone' => ['required', 'string', 'max:20',],
            'address' => ['required', 'string', 'max:255',],
            'email' => ['nullable', 'email', 'max:255',],
            'is_active' => ['sometimes', 'boolean',],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama Supplier wajib diisi.',
            'name.string' => 'Nama Supplier harus berupa teks.',
            'name.max' => 'Nama Supplier maksimal 255 karakter.',

            'person_responsible.string' => 'Nama penanggung jawab harus berupa teks.',
            'person_responsible.max' => 'Nama penanggung jawab maksimal 255 karakter.',

            'phone.required' => 'Nomor telepon Supplier wajib diisi.',
            'phone.string' => 'Nomor telepon Supplier harus berupa teks.',
            'phone.max' => 'Nomor telepon Supplier maksimal 20 karakter.',

            'address.required' => 'Alamat Supplier wajib diisi.',
            'address.string' => 'Alamat Supplier harus berupa teks.',
            'address.max' => 'Alamat Supplier maksimal 255 karakter.',

            'email.email' => 'Format email Supplier tidak valid.',
            'email.max' => 'Email Supplier maksimal 255 karakter.',

            'is_active.boolean' => 'Status aktif tidak valid.',
        ];
    }
}
