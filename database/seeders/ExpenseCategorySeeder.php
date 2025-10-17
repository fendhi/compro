<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Parent Categories
        $operasional = DB::table('expense_categories')->insertGetId([
            'nama' => 'Operasional',
            'parent_id' => null,
            'deskripsi' => 'Biaya operasional harian',
            'icon' => 'fas fa-cogs',
            'warna' => '#3B82F6',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $gaji = DB::table('expense_categories')->insertGetId([
            'nama' => 'Gaji & Upah',
            'parent_id' => null,
            'deskripsi' => 'Gaji karyawan dan tunjangan',
            'icon' => 'fas fa-users',
            'warna' => '#10B981',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $marketing = DB::table('expense_categories')->insertGetId([
            'nama' => 'Marketing',
            'parent_id' => null,
            'deskripsi' => 'Biaya promosi dan marketing',
            'icon' => 'fas fa-bullhorn',
            'warna' => '#F59E0B',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $inventory = DB::table('expense_categories')->insertGetId([
            'nama' => 'Inventory',
            'parent_id' => null,
            'deskripsi' => 'Pembelian stok dan packaging',
            'icon' => 'fas fa-boxes',
            'warna' => '#8B5CF6',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $maintenance = DB::table('expense_categories')->insertGetId([
            'nama' => 'Maintenance',
            'parent_id' => null,
            'deskripsi' => 'Perbaikan dan pemeliharaan',
            'icon' => 'fas fa-tools',
            'warna' => '#EF4444',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $lainnya = DB::table('expense_categories')->insertGetId([
            'nama' => 'Lain-lain',
            'parent_id' => null,
            'deskripsi' => 'Pengeluaran lainnya',
            'icon' => 'fas fa-ellipsis-h',
            'warna' => '#6B7280',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Sub-categories for Operasional
        $subCategories = [
            // Operasional
            ['nama' => 'Listrik', 'parent_id' => $operasional, 'icon' => 'fas fa-bolt', 'warna' => '#FBBF24'],
            ['nama' => 'Air', 'parent_id' => $operasional, 'icon' => 'fas fa-tint', 'warna' => '#3B82F6'],
            ['nama' => 'Internet', 'parent_id' => $operasional, 'icon' => 'fas fa-wifi', 'warna' => '#8B5CF6'],
            ['nama' => 'Telepon', 'parent_id' => $operasional, 'icon' => 'fas fa-phone', 'warna' => '#10B981'],
            
            // Gaji & Upah
            ['nama' => 'Gaji Karyawan', 'parent_id' => $gaji, 'icon' => 'fas fa-money-bill-wave', 'warna' => '#10B981'],
            ['nama' => 'Bonus', 'parent_id' => $gaji, 'icon' => 'fas fa-gift', 'warna' => '#F59E0B'],
            ['nama' => 'Lembur', 'parent_id' => $gaji, 'icon' => 'fas fa-clock', 'warna' => '#EF4444'],
            
            // Marketing
            ['nama' => 'Iklan', 'parent_id' => $marketing, 'icon' => 'fas fa-ad', 'warna' => '#F59E0B'],
            ['nama' => 'Promosi', 'parent_id' => $marketing, 'icon' => 'fas fa-percent', 'warna' => '#EF4444'],
            ['nama' => 'Giveaway', 'parent_id' => $marketing, 'icon' => 'fas fa-gift', 'warna' => '#8B5CF6'],
            
            // Inventory
            ['nama' => 'Pembelian Stok', 'parent_id' => $inventory, 'icon' => 'fas fa-shopping-cart', 'warna' => '#8B5CF6'],
            ['nama' => 'Ongkir Supplier', 'parent_id' => $inventory, 'icon' => 'fas fa-shipping-fast', 'warna' => '#3B82F6'],
            ['nama' => 'Packaging', 'parent_id' => $inventory, 'icon' => 'fas fa-box', 'warna' => '#F59E0B'],
            
            // Maintenance
            ['nama' => 'Perbaikan', 'parent_id' => $maintenance, 'icon' => 'fas fa-wrench', 'warna' => '#EF4444'],
            ['nama' => 'Service Alat', 'parent_id' => $maintenance, 'icon' => 'fas fa-screwdriver', 'warna' => '#F59E0B'],
            ['nama' => 'Renovasi', 'parent_id' => $maintenance, 'icon' => 'fas fa-paint-roller', 'warna' => '#8B5CF6'],
            
            // Lain-lain
            ['nama' => 'Transportasi', 'parent_id' => $lainnya, 'icon' => 'fas fa-car', 'warna' => '#3B82F6'],
            ['nama' => 'Konsumsi', 'parent_id' => $lainnya, 'icon' => 'fas fa-utensils', 'warna' => '#10B981'],
            ['nama' => 'ATK', 'parent_id' => $lainnya, 'icon' => 'fas fa-pencil-alt', 'warna' => '#6B7280'],
        ];

        foreach ($subCategories as $category) {
            DB::table('expense_categories')->insert([
                'nama' => $category['nama'],
                'parent_id' => $category['parent_id'],
                'deskripsi' => null,
                'icon' => $category['icon'],
                'warna' => $category['warna'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
