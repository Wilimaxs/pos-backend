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
            'sale-history.view-all' => 'Melihat history penjualan dari seluruh store.',

            // Piutang member.
            'receivable.view' => 'Melihat piutang member pada store tempat employee ditugaskan.',
            'receivable.view-all' => 'Melihat piutang member dari seluruh store.',
            'receivable.payment' => 'Menerima pembayaran piutang pada store asal penjualan.',

            // Produk dan harga.
            'product.view' => 'Melihat katalog dan detail produk.',
            'product.manage-price' => 'Mengelola harga jual sesuai cakupan store yang diizinkan.',
            'product.manage' => 'Mengelola data master produk dan stok minimum.',

            // Pembelian hanya dilakukan di Gudang Pusat.
            'purchase.view' => 'Melihat pembelian langsung pada Gudang Pusat.',
            'purchase.view-all' => 'Melihat seluruh data pembelian tanpa dibatasi penugasan store employee.',
            'purchase.create' => 'Mencatat pembelian barang yang sudah diterima dan lunas di Gudang Pusat.',

            // Inventori.
            'inventory.view' => 'Melihat stok pada store tempat employee ditugaskan.',
            'inventory.view-all' => 'Melihat stok dari seluruh store.',
            'inventory.history' => 'Melihat histori perubahan stok sesuai cakupan store.',

            // Transfer stok: kirim dari pusat dan terima di cabang.
            'stock-transfer.view' => 'Melihat transfer stok yang berkaitan dengan store employee.',
            'stock-transfer.view-all' => 'Melihat transfer stok dari seluruh store.',
            'stock-transfer.create' => 'Membuat dokumen transfer stok dari Gudang Pusat ke cabang.',
            'stock-transfer.send' => 'Mengirim transfer dan mengurangi stok Gudang Pusat.',
            'stock-transfer.receive' => 'Menerima transfer dan mencatat jumlah aktual di cabang tujuan.',

            // Stok opname.
            'stock-opname.view' => 'Melihat stok opname pada store tempat employee ditugaskan.',
            'stock-opname.view-all' => 'Melihat stok opname dari seluruh store.',
            'stock-opname.manage' => 'Menjalankan dan menyelesaikan stok opname pada store employee.',

            // Member.
            'member.view' => 'Melihat daftar dan informasi member.',
            'member.create' => 'Mendaftarkan member baru.',
            'member.edit' => 'Mengubah informasi member.',

            // Store.
            'store.view' => 'Melihat store tempat employee ditugaskan.',
            'store.view-all' => 'Melihat seluruh pusat dan cabang.',
            'store.create' => 'Membuat pusat atau cabang baru.',
            'store.edit' => 'Mengubah informasi pusat atau cabang.',

            // Supplier digunakan untuk pembelian di Gudang Pusat.
            'supplier.view' => 'Melihat daftar dan informasi supplier.',
            'supplier.create' => 'Mendaftarkan supplier baru.',
            'supplier.edit' => 'Mengubah informasi supplier.',

            // Employee dan delegasi permission.
            'employee.view' => 'Melihat employee pada store tempat pengguna ditugaskan.',
            'employee.view-all' => 'Melihat employee dari seluruh store.',
            'employee.create' => 'Membuat akun employee dan menentukan penugasan store.',
            'employee.create-all' => 'Membuat akun employee dan menentukan penugasan store dari seluruh store.',
            'employee.edit' => 'Mengubah akun, penugasan store, dan status aktif employee.',
            'employee.edit-all' => 'Mengubah akun, penugasan store, dan status aktif employee dari seluruh store.',
            'employee.manage-permission' => 'Memberikan dan mencabut permission employee dalam batas kewenangan pengguna.',
            'employee.manage-permission-all' => 'Memberikan dan mencabut permission employee dari seluruh store.',
        ];

        foreach ($permissions as $name => $description) {
            Permission::query()->updateOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }
    }
}
