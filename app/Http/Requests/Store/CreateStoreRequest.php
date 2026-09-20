<?php

namespace App\Http\Requests\Store;

use App\Enum\StoreType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateStoreRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(StoreType::class)],
            'is_active' => ['sometimes', 'boolean'],
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

            'type.required' => 'Tipe Store wajib dipilih.',
            'type.enum' => 'Tipe Store harus CENTRAL atau BRANCH.',

            'is_active.boolean' => 'Status aktif tidak valid.',
        ];
    }
}
