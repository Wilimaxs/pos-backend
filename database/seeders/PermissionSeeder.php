<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the application's permissions.
     */
    public function run(): void
    {
        $permissions = [
            // Penjualan dan history.
            'sale.create' => 'Menjalankan checkout, pembayaran, DP member, melanjutkan transaksi Pending sendiri, dan mencetak struk.',
            'sale-history.view' => 'Melihat history penjualan pada store tempat employee ditugaskan.',

            // Piutang member.
            'receivable.view' => 'Melihat piutang member pada store tempat employee ditugaskan.',
            'receivable.payment' => 'Menerima pembayaran piutang pada store asal penjualan.',

            // Produk dan harga.
            'product.view' => 'Melihat katalog dan detail produk.',
            'product.manage-price' => 'Mengelola harga jual sesuai cakupan store yang diizinkan.',
            'product.manage' => 'Mengelola data master produk dan stok minimum.',

            // Pembelian hanya dilakukan di Gudang Pusat.
            'purchase.view' => 'Melihat pembelian langsung pada Gudang Pusat.',
            'purchase.create' => 'Mencatat pembelian barang yang sudah diterima dan lunas di Gudang Pusat.',

            // Inventori.
            'inventory.view' => 'Melihat stok pada store tempat employee ditugaskan.',
            'inventory.history' => 'Melihat histori perubahan stok sesuai cakupan store.',

            // Transfer stok: kirim dari pusat dan terima di cabang.
            'stock-transfer.view' => 'Melihat transfer stok yang berkaitan dengan store employee.',
            'stock-transfer.create' => 'Membuat dokumen transfer stok dari Gudang Pusat ke cabang.',
            'stock-transfer.send' => 'Mengirim transfer dan mengurangi stok Gudang Pusat.',
            'stock-transfer.receive' => 'Menerima transfer dan mencatat jumlah aktual di cabang tujuan.',

            // Stok opname.
            'stock-opname.view' => 'Melihat stok opname pada store tempat employee ditugaskan.',
            'stock-opname.manage' => 'Menjalankan dan menyelesaikan stok opname pada store employee.',

            // Member.
            'member.view' => 'Melihat daftar dan informasi member.',
            'member.create' => 'Mendaftarkan member baru.',
            'member.edit' => 'Mengubah informasi member.',

            // Store.
            'store.view' => 'Melihat store tempat employee ditugaskan.',
            'store.create' => 'Membuat pusat atau cabang baru.',
            'store.edit' => 'Mengubah informasi pusat atau cabang.',

            // Supplier digunakan untuk pembelian di Gudang Pusat.
            'supplier.view' => 'Melihat daftar dan informasi supplier.',
            'supplier.create' => 'Mendaftarkan supplier baru.',
            'supplier.edit' => 'Mengubah informasi supplier.',

            // Employee
            'employee.view' => 'Melihat employee pada store tempat pengguna ditugaskan.',
            'employee.create' => 'Membuat akun employee dan menentukan penugasan store.',
            'employee.edit' => 'Mengubah akun, penugasan store, dan status aktif employee.',

            // Delegasi permission.
            'employee.manage-permission' => 'Memberikan dan mencabut permission employee dalam batas kewenangan pengguna.',
        ];

        Permission::query()
            ->where('name', 'like', '%-all')
            ->delete();

        foreach ($permissions as $name => $description) {
            $isOwnerOnly = str_contains($name, '.manage');

            Permission::query()->updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'is_owner_only' => $isOwnerOnly,
                ],
            );
        }
    }
}
