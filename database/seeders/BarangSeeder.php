<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Kategori;

class BarangSeeder extends Seeder
{
    /**
     * Calculate harga modal (HPP) dari harga jual
     * Target margin: 30-40%
     * 
     * Formula: Harga Modal = Harga Jual / (1 + Margin%)
     * Contoh: Harga Jual 100rb, Margin 35% → HPP = 100rb / 1.35 = 74rb
     */
    private function calculateHargaModal($hargaJual, $marginPercent = 35)
    {
        return round($hargaJual / (1 + ($marginPercent / 100)), -3); // Round ke ribuan terdekat
    }

    public function run()
    {
        // Get all categories
        $kategoris = Kategori::all()->keyBy('nama');
        
        if ($kategoris->isEmpty()) {
            $this->command->error('❌ Tidak ada kategori! Run KategoriSeeder dulu.');
            return;
        }

        $this->command->info('🔄 Menghapus data barang lama...');
        
        // Hapus stock movements dulu (foreign key constraint)
        \DB::table('stock_movements')->delete();
        
        // Hapus transaksi details (foreign key constraint)
        \DB::table('transaksi_details')->delete();
        
        // Baru hapus barang
        Barang::query()->delete();
        
        $this->command->info('✅ Data lama berhasil dihapus!');
        $this->command->newLine();

        $products = [];

        // ==================== POD SYSTEM & DEVICE ====================
        if ($kategoris->has('Pod System & Device')) {
            $kategoriId = $kategoris['Pod System & Device']->id;
            $products = array_merge($products, [
                // Margin 30-35% untuk pod (produk high-end)
                ['kode' => 'POD001', 'nama' => 'Voopoo Drag X Pod Kit', 'kategori_id' => $kategoriId, 'harga' => 450000, 'margin' => 30, 'stok' => 15, 'satuan' => 'Unit', 'stok_min' => 3],
                ['kode' => 'POD002', 'nama' => 'Vaporesso Xros 3 Mini', 'kategori_id' => $kategoriId, 'harga' => 280000, 'margin' => 32, 'stok' => 20, 'satuan' => 'Unit', 'stok_min' => 5],
                ['kode' => 'POD003', 'nama' => 'Uwell Caliburn G2', 'kategori_id' => $kategoriId, 'harga' => 320000, 'margin' => 30, 'stok' => 18, 'satuan' => 'Unit', 'stok_min' => 4],
                ['kode' => 'POD004', 'nama' => 'GeekVape Wenax K1', 'kategori_id' => $kategoriId, 'harga' => 200000, 'margin' => 33, 'stok' => 25, 'satuan' => 'Unit', 'stok_min' => 5],
                ['kode' => 'POD005', 'nama' => 'Smok Nord 4 Kit', 'kategori_id' => $kategoriId, 'harga' => 380000, 'margin' => 30, 'stok' => 12, 'satuan' => 'Unit', 'stok_min' => 3],
                ['kode' => 'POD006', 'nama' => 'Vaporesso Gen PT60', 'kategori_id' => $kategoriId, 'harga' => 550000, 'margin' => 30, 'stok' => 10, 'satuan' => 'Unit', 'stok_min' => 2],
                ['kode' => 'POD007', 'nama' => 'Lost Vape Ursa Baby Pro', 'kategori_id' => $kategoriId, 'harga' => 480000, 'margin' => 32, 'stok' => 8, 'satuan' => 'Unit', 'stok_min' => 2],
                ['kode' => 'POD008', 'nama' => 'Aspire Gotek X Pod Kit', 'kategori_id' => $kategoriId, 'harga' => 250000, 'margin' => 35, 'stok' => 15, 'satuan' => 'Unit', 'stok_min' => 4],
            ]);
        }

        // ==================== LIQUID SALTNIC 30ML ====================
        if ($kategoris->has('Liquid Saltnic 30ml')) {
            $kategoriId = $kategoris['Liquid Saltnic 30ml']->id;
            $products = array_merge($products, [
                // Hexs Series (Best Seller) - Margin 38%
                ['kode' => 'LIQ001', 'nama' => 'Hexs Mango Ice 30ml 25mg', 'kategori_id' => $kategoriId, 'harga' => 45000, 'margin' => 38, 'stok' => 80, 'satuan' => 'Botol', 'stok_min' => 20],
                ['kode' => 'LIQ002', 'nama' => 'Hexs Grape Ice 30ml 25mg', 'kategori_id' => $kategoriId, 'harga' => 45000, 'margin' => 38, 'stok' => 75, 'satuan' => 'Botol', 'stok_min' => 20],
                ['kode' => 'LIQ003', 'nama' => 'Hexs Strawberry Watermelon 30ml 25mg', 'kategori_id' => $kategoriId, 'harga' => 45000, 'margin' => 38, 'stok' => 70, 'satuan' => 'Botol', 'stok_min' => 15],
                ['kode' => 'LIQ004', 'nama' => 'Hexs Lychee Ice 30ml 25mg', 'kategori_id' => $kategoriId, 'harga' => 45000, 'margin' => 38, 'stok' => 65, 'satuan' => 'Botol', 'stok_min' => 15],
                
                // Yummy Series - Margin 40% (murah, laku keras)
                ['kode' => 'LIQ005', 'nama' => 'Yummy Mixed Berries 30ml 30mg', 'kategori_id' => $kategoriId, 'harga' => 40000, 'margin' => 40, 'stok' => 60, 'satuan' => 'Botol', 'stok_min' => 15],
                ['kode' => 'LIQ006', 'nama' => 'Yummy Energy Drink 30ml 30mg', 'kategori_id' => $kategoriId, 'harga' => 40000, 'margin' => 40, 'stok' => 55, 'satuan' => 'Botol', 'stok_min' => 15],
                ['kode' => 'LIQ007', 'nama' => 'Yummy Cola Ice 30ml 30mg', 'kategori_id' => $kategoriId, 'harga' => 40000, 'margin' => 40, 'stok' => 50, 'satuan' => 'Botol', 'stok_min' => 12],
                
                // Saltnic Premium Import - Margin 30% (import, margin lebih tipis)
                ['kode' => 'LIQ008', 'nama' => 'Nasty Juice Bad Blood 30ml 35mg', 'kategori_id' => $kategoriId, 'harga' => 85000, 'margin' => 30, 'stok' => 40, 'satuan' => 'Botol', 'stok_min' => 10],
                ['kode' => 'LIQ009', 'nama' => 'Nasty Juice Slow Blow 30ml 35mg', 'kategori_id' => $kategoriId, 'harga' => 85000, 'margin' => 30, 'stok' => 35, 'satuan' => 'Botol', 'stok_min' => 10],
                ['kode' => 'LIQ010', 'nama' => 'Dinner Lady Lemon Tart 30ml 30mg', 'kategori_id' => $kategoriId, 'harga' => 95000, 'margin' => 30, 'stok' => 30, 'satuan' => 'Botol', 'stok_min' => 8],
                
                // Saltnic Lokal Murah - Margin 40%
                ['kode' => 'LIQ011', 'nama' => 'Aisu Blueberry Ice 30ml 20mg', 'kategori_id' => $kategoriId, 'harga' => 35000, 'margin' => 40, 'stok' => 90, 'satuan' => 'Botol', 'stok_min' => 25],
                ['kode' => 'LIQ012', 'nama' => 'Shweet Vanilla Cream 30ml 25mg', 'kategori_id' => $kategoriId, 'harga' => 38000, 'margin' => 38, 'stok' => 70, 'satuan' => 'Botol', 'stok_min' => 20],
                ['kode' => 'LIQ013', 'nama' => 'Dreamods Milk Tea 30ml 30mg', 'kategori_id' => $kategoriId, 'harga' => 42000, 'margin' => 37, 'stok' => 65, 'satuan' => 'Botol', 'stok_min' => 18],
                ['kode' => 'LIQ014', 'nama' => 'Swisskinn Spearmint 30ml 25mg', 'kategori_id' => $kategoriId, 'harga' => 40000, 'margin' => 38, 'stok' => 55, 'satuan' => 'Botol', 'stok_min' => 15],
            ]);
        }

        // ==================== LIQUID FREEBASE 60ML ====================
        if ($kategoris->has('Liquid Freebase 60ml')) {
            $kategoriId = $kategoris['Liquid Freebase 60ml']->id;
            $products = array_merge($products, [
                ['kode' => 'LIQ101', 'nama' => 'Hexs Freebase Mango Lychee 60ml 3mg', 'kategori_id' => $kategoriId, 'harga' => 65000, 'margin' => 32, 'stok' => 50, 'satuan' => 'Botol', 'stok_min' => 12],
                ['kode' => 'LIQ102', 'nama' => 'Yummy Freebase Strawberry 60ml 3mg', 'kategori_id' => $kategoriId, 'harga' => 60000, 'margin' => 40, 'stok' => 45, 'satuan' => 'Botol', 'stok_min' => 12],
                ['kode' => 'LIQ103', 'nama' => 'Nasty Juice Cushman 60ml 6mg', 'kategori_id' => $kategoriId, 'harga' => 120000, 'margin' => 30, 'stok' => 30, 'satuan' => 'Botol', 'stok_min' => 8],
                ['kode' => 'LIQ104', 'nama' => 'Naked 100 Hawaiian Pog 60ml 3mg', 'kategori_id' => $kategoriId, 'harga' => 150000, 'margin' => 30, 'stok' => 25, 'satuan' => 'Botol', 'stok_min' => 6],
                ['kode' => 'LIQ105', 'nama' => 'Twist Pink Punch 60ml 3mg', 'kategori_id' => $kategoriId, 'harga' => 140000, 'margin' => 30, 'stok' => 20, 'satuan' => 'Botol', 'stok_min' => 6],
                ['kode' => 'LIQ106', 'nama' => 'Jam Monster Strawberry 100ml 3mg', 'kategori_id' => $kategoriId, 'harga' => 180000, 'margin' => 30, 'stok' => 15, 'satuan' => 'Botol', 'stok_min' => 4],
            ]);
        }

        // ==================== DISPOSABLE VAPE ====================
        if ($kategoris->has('Disposable Vape')) {
            $kategoriId = $kategoris['Disposable Vape']->id;
            $products = array_merge($products, [
                ['kode' => 'DISP01', 'nama' => 'Hyppe Max Flow 2000 Puffs - Mango Ice', 'kategori_id' => $kategoriId, 'harga' => 95000, 'margin' => 35, 'stok' => 100, 'satuan' => 'Pcs', 'stok_min' => 30],
                ['kode' => 'DISP02', 'nama' => 'Hyppe Max Flow 2000 Puffs - Grape Ice', 'kategori_id' => $kategoriId, 'harga' => 95000, 'margin' => 35, 'stok' => 95, 'satuan' => 'Pcs', 'stok_min' => 30],
                ['kode' => 'DISP03', 'nama' => 'Elf Bar BC5000 - Strawberry Kiwi', 'kategori_id' => $kategoriId, 'harga' => 120000, 'margin' => 35, 'stok' => 80, 'satuan' => 'Pcs', 'stok_min' => 25],
                ['kode' => 'DISP04', 'nama' => 'Iget Legend 4000 Puffs - Mixed Berries', 'kategori_id' => $kategoriId, 'harga' => 110000, 'margin' => 35, 'stok' => 75, 'satuan' => 'Pcs', 'stok_min' => 25],
                ['kode' => 'DISP05', 'nama' => 'Yuoto 1500 Puffs - Energy Drink', 'kategori_id' => $kategoriId, 'harga' => 75000, 'margin' => 38, 'stok' => 90, 'satuan' => 'Pcs', 'stok_min' => 30],
                ['kode' => 'DISP06', 'nama' => 'Puff Bar Plus 800 Puffs - Lychee Ice', 'kategori_id' => $kategoriId, 'harga' => 55000, 'margin' => 38, 'stok' => 120, 'satuan' => 'Pcs', 'stok_min' => 40],
                ['kode' => 'DISP07', 'nama' => 'HQD Cuvie 300 Puffs - Cola Ice', 'kategori_id' => $kategoriId, 'harga' => 35000, 'margin' => 40, 'stok' => 150, 'satuan' => 'Pcs', 'stok_min' => 50],
            ]);
        }

        // ==================== POD CARTRIDGE & COIL ====================
        if ($kategoris->has('Pod Cartridge & Coil')) {
            $kategoriId = $kategoris['Pod Cartridge & Coil']->id;
            $products = array_merge($products, [
                ['kode' => 'COIL01', 'nama' => 'Voopoo PnP Coil 0.8ohm', 'kategori_id' => $kategoriId, 'harga' => 45000, 'margin' => 35, 'stok' => 60, 'satuan' => 'Pcs', 'stok_min' => 20],
                ['kode' => 'COIL02', 'nama' => 'Vaporesso GTX Coil 0.8ohm', 'kategori_id' => $kategoriId, 'harga' => 40000, 'margin' => 35, 'stok' => 70, 'satuan' => 'Pcs', 'stok_min' => 20],
                ['kode' => 'COIL03', 'nama' => 'Uwell Caliburn G Coil 1.0ohm', 'kategori_id' => $kategoriId, 'harga' => 38000, 'margin' => 38, 'stok' => 80, 'satuan' => 'Pcs', 'stok_min' => 25],
                ['kode' => 'CART01', 'nama' => 'Smok Nord 4 Cartridge', 'kategori_id' => $kategoriId, 'harga' => 50000, 'margin' => 35, 'stok' => 50, 'satuan' => 'Pcs', 'stok_min' => 15],
                ['kode' => 'CART02', 'nama' => 'GeekVape Wenax Pod Cartridge', 'kategori_id' => $kategoriId, 'harga' => 42000, 'margin' => 38, 'stok' => 55, 'satuan' => 'Pcs', 'stok_min' => 15],
                ['kode' => 'COIL04', 'nama' => 'Voopoo PnP Mesh Coil 0.6ohm', 'kategori_id' => $kategoriId, 'harga' => 50000, 'margin' => 35, 'stok' => 45, 'satuan' => 'Pcs', 'stok_min' => 15],
            ]);
        }

        // ==================== BATTERY & CHARGER ====================
        if ($kategoris->has('Battery & Charger')) {
            $kategoriId = $kategoris['Battery & Charger']->id;
            $products = array_merge($products, [
                ['kode' => 'BAT001', 'nama' => 'Sony VTC6 18650 3000mAh', 'kategori_id' => $kategoriId, 'harga' => 120000, 'margin' => 30, 'stok' => 40, 'satuan' => 'Pcs', 'stok_min' => 10],
                ['kode' => 'BAT002', 'nama' => 'Samsung 25R 18650 2500mAh', 'kategori_id' => $kategoriId, 'harga' => 100000, 'margin' => 30, 'stok' => 50, 'satuan' => 'Pcs', 'stok_min' => 12],
                ['kode' => 'BAT003', 'nama' => 'LG HG2 18650 3000mAh', 'kategori_id' => $kategoriId, 'harga' => 110000, 'margin' => 30, 'stok' => 45, 'satuan' => 'Pcs', 'stok_min' => 12],
                ['kode' => 'BAT004', 'nama' => 'Mxjo 21700 4000mAh', 'kategori_id' => $kategoriId, 'harga' => 140000, 'margin' => 30, 'stok' => 30, 'satuan' => 'Pcs', 'stok_min' => 8],
                ['kode' => 'CHG001', 'nama' => 'Nitecore i2 Charger', 'kategori_id' => $kategoriId, 'harga' => 150000, 'margin' => 32, 'stok' => 25, 'satuan' => 'Unit', 'stok_min' => 6],
                ['kode' => 'CHG002', 'nama' => 'Efest LUC V4 Charger', 'kategori_id' => $kategoriId, 'harga' => 200000, 'margin' => 32, 'stok' => 15, 'satuan' => 'Unit', 'stok_min' => 4],
            ]);
        }

        // ==================== AKSESORIS VAPE ====================
        if ($kategoris->has('Aksesoris Vape')) {
            $kategoriId = $kategoris['Aksesoris Vape']->id;
            $products = array_merge($products, [
                ['kode' => 'ACC001', 'nama' => 'Cotton Bacon Prime', 'kategori_id' => $kategoriId, 'harga' => 35000, 'margin' => 38, 'stok' => 100, 'satuan' => 'Pack', 'stok_min' => 30],
                ['kode' => 'ACC002', 'nama' => 'Cotton Bacon V2', 'kategori_id' => $kategoriId, 'harga' => 30000, 'margin' => 40, 'stok' => 120, 'satuan' => 'Pack', 'stok_min' => 35],
                ['kode' => 'ACC003', 'nama' => 'Kanthal A1 Wire 24ga', 'kategori_id' => $kategoriId, 'harga' => 25000, 'margin' => 40, 'stok' => 80, 'satuan' => 'Meter', 'stok_min' => 20],
                ['kode' => 'ACC004', 'nama' => 'Ni80 Wire 26ga', 'kategori_id' => $kategoriId, 'harga' => 30000, 'margin' => 38, 'stok' => 70, 'satuan' => 'Meter', 'stok_min' => 20],
                ['kode' => 'ACC005', 'nama' => 'Drip Tip 810 Resin', 'kategori_id' => $kategoriId, 'harga' => 50000, 'margin' => 35, 'stok' => 40, 'satuan' => 'Pcs', 'stok_min' => 10],
                ['kode' => 'ACC006', 'nama' => 'Battery Case Silicone (2 slot)', 'kategori_id' => $kategoriId, 'harga' => 20000, 'margin' => 40, 'stok' => 90, 'satuan' => 'Pcs', 'stok_min' => 25],
                ['kode' => 'ACC007', 'nama' => 'Pod Carrying Case Premium', 'kategori_id' => $kategoriId, 'harga' => 75000, 'margin' => 35, 'stok' => 35, 'satuan' => 'Pcs', 'stok_min' => 10],
                ['kode' => 'ACC008', 'nama' => 'Vape Cleaning Kit 6 in 1', 'kategori_id' => $kategoriId, 'harga' => 45000, 'margin' => 38, 'stok' => 50, 'satuan' => 'Set', 'stok_min' => 12],
                ['kode' => 'ACC009', 'nama' => 'Lanyard Pod Premium', 'kategori_id' => $kategoriId, 'harga' => 15000, 'margin' => 40, 'stok' => 100, 'satuan' => 'Pcs', 'stok_min' => 30],
            ]);
        }

        // ==================== ATOMIZER & TANK ====================
        if ($kategoris->has('Atomizer & Tank')) {
            $kategoriId = $kategoris['Atomizer & Tank']->id;
            $products = array_merge($products, [
                ['kode' => 'ATM001', 'nama' => 'GeekVape Zeus RTA', 'kategori_id' => $kategoriId, 'harga' => 450000, 'margin' => 30, 'stok' => 12, 'satuan' => 'Unit', 'stok_min' => 3],
                ['kode' => 'ATM002', 'nama' => 'Vaporesso NRG Tank', 'kategori_id' => $kategoriId, 'harga' => 350000, 'margin' => 30, 'stok' => 15, 'satuan' => 'Unit', 'stok_min' => 4],
                ['kode' => 'ATM003', 'nama' => 'Dead Rabbit RDA V2', 'kategori_id' => $kategoriId, 'harga' => 550000, 'margin' => 30, 'stok' => 8, 'satuan' => 'Unit', 'stok_min' => 2],
                ['kode' => 'ATM004', 'nama' => 'Smok TFV16 Sub-Ohm Tank', 'kategori_id' => $kategoriId, 'harga' => 400000, 'margin' => 30, 'stok' => 10, 'satuan' => 'Unit', 'stok_min' => 3],
            ]);
        }

        // Create all products with harga_modal calculation
        $totalCreated = 0;
        $this->command->info('📦 Membuat produk dengan harga modal...');
        
        foreach ($products as $product) {
            $margin = $product['margin'] ?? 35; // Default 35% jika tidak ada
            $hargaModal = $this->calculateHargaModal($product['harga'], $margin);
            
            Barang::create([
                'kode_barang' => $product['kode'],
                'nama' => $product['nama'],
                'kategori_id' => $product['kategori_id'],
                'harga' => $product['harga'],
                'harga_modal' => $hargaModal,
                'stok' => $product['stok'],
                'satuan' => $product['satuan'],
                'stok_minimum' => $product['stok_min'],
                'is_active' => true,
            ]);
            $totalCreated++;
            
            // Show progress every 10 products
            if ($totalCreated % 10 == 0) {
                $this->command->info("   ✓ {$totalCreated} produk...");
            }
        }

        $this->command->newLine();
        $this->command->info("✅ Berhasil membuat {$totalCreated} produk vape store Orind Vapor!");
        $this->command->info('📦 Kategori: ' . $kategoris->count());
        $this->command->info('🛍️  Total Produk: ' . $totalCreated);
        $this->command->newLine();
        $this->command->info('💰 MARGIN INFORMATION:');
        $this->command->line('   - Pod System: 30-35% margin');
        $this->command->line('   - Liquid Lokal: 37-40% margin');
        $this->command->line('   - Liquid Import: 30% margin');
        $this->command->line('   - Disposable: 35-38% margin');
        $this->command->line('   - Coil/Cartridge: 35-40% margin');
        $this->command->line('   - Battery: 30-32% margin');
        $this->command->line('   - Accessories: 38-40% margin');
        $this->command->newLine();
        $this->command->info('🎯 Breakdown per kategori:');
        
        foreach ($kategoris as $kategori) {
            $count = Barang::where('kategori_id', $kategori->id)->count();
            if ($count > 0) {
                $this->command->line("   - {$kategori->nama}: {$count} produk");
            }
        }
        
        // Show sample profit calculation
        $this->command->newLine();
        $this->command->info('📊 SAMPLE PROFIT CALCULATION:');
        $sample = Barang::with('kategori')->first();
        if ($sample) {
            $profit = $sample->harga - $sample->harga_modal;
            $marginActual = (($profit / $sample->harga_modal) * 100);
            $this->command->line("   Produk: {$sample->nama}");
            $this->command->line("   Harga Jual: Rp " . number_format($sample->harga, 0, ',', '.'));
            $this->command->line("   Harga Modal: Rp " . number_format($sample->harga_modal, 0, ',', '.'));
            $this->command->line("   Profit/unit: Rp " . number_format($profit, 0, ',', '.'));
            $this->command->line("   Margin: " . number_format($marginActual, 1) . "%");
        }
    }
}
