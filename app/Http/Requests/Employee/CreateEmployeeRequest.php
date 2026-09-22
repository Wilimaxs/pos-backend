<?php

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEmployeeRequest extends FormRequest
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
            'store_code' => ['bail','required', 'string', Rule::exists('stores', 'store_code')
                ->where('is_active', true)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['bail','required', 'string', 'max:255', 'unique:employees,phone'],
            'password' => ['required', 'string', 'min:8'],
            'address' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_code.required' => 'Store wajib dipilih.',
            'store_code.string' => 'Kode store harus berupa teks.',
            'store_code.exists' => 'Store tidak ditemukan atau sudah tidak aktif.',

            'name.required' => 'Nama pegawai wajib diisi.',
            'name.string' => 'Nama pegawai harus berupa teks.',
            'name.max' => 'Nama pegawai maksimal 255 karakter.',

            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',

            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'phone.unique' => 'Nomor telepon sudah digunakan oleh pegawai lain.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.string' => 'Kata sandi harus berupa teks.',
            'password.min' => 'Kata sandi minimal 8 karakter.',

            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 255 karakter.',

            'position.required' => 'Jabatan pegawai wajib diisi.',
            'position.string' => 'Jabatan pegawai harus berupa teks.',
            'position.max' => 'Jabatan pegawai maksimal 255 karakter.',
        ];
    }
}
