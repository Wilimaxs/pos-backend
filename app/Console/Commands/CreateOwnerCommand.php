<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:create-owner-')]
#[Description('Create first owner account')]
class CreateOwnerCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (Employee::query()->where('employees.is_owner', true)->exists()) {
            $this->error('Akun owner sudah tersedia.');

            return self::FAILURE;
        }

        $name = trim((string)$this->ask('nama owner: '));
        $phone = trim((string)$this->ask('nomor telepon: '));
        $email = trim((string)$this->ask('email opsional: ', ''));

        $password = (string)$this->secret('Kata sandi');
        $passwordConfirmation = (string)$this->secret('Konfirmasi kata sandi');

        if ($name === '') {
            $this->error('Nama owner tidak boleh kosong.');

            return self::FAILURE;
        }

        if ($phone === '') {
            $this->error('Nomor telepon tidak boleh kosong.');

            return self::FAILURE;
        }

        if (mb_strlen($phone) > 20) {
            $this->error('Nomor telepon tidak boleh lebih dari 20 karakter.');

            return self::FAILURE;
        }

        if (Employee::query()->where('employees.phone', $phone)->exists()) {
            $this->error('Nomor telepon sudah digunakan.');

            return self::FAILURE;
        }

        if (mb_strlen($password) < 8) {
            $this->error('Kata sandi harus memiliki panjang minimal 8 karakter.');

            return self::FAILURE;
        }

        if ($password !== $passwordConfirmation) {
            $this->error('Kata sandi tidak cocok.');

            return self::FAILURE;
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email tidak valid.');

            return self::FAILURE;
        }

        $owner = Employee::query()->forceCreate([
            'store_code' => null,
            'name' => $name,
            'email' => $email !== '' ? $email : null,
            'phone' => $phone,
            'password' => $password,
            'address' => null,
            'position' => 'Owner',
            'is_owner' => true,
            'is_active' => true,
        ]);


        $this->info('Akun owner berhasil dibuat.');
        $this->line(
            'Employee code: ' . $owner->employee_code
        );

        return self::SUCCESS;
    }
}
