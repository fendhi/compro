<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Pod System & Device',
                'deskripsi' => 'Perangkat vape elektronik (pod system, box mod, starter kit)'
            ],
            [
                'nama' => 'Liquid Saltnic 30ml',
                'deskripsi' => 'E-liquid saltnic 30ml dengan berbagai varian rasa (untuk pod system)'
            ],
            [
                'nama' => 'Liquid Freebase 60ml',
                'deskripsi' => 'E-liquid freebase 60ml nikotin rendah (untuk mod/sub-ohm)'
            ],
            [
                'nama' => 'Disposable Vape',
                'deskripsi' => 'Vape sekali pakai siap puff (300-2500 puffs)'
            ],
            [
                'nama' => 'Pod Cartridge & Coil',
                'deskripsi' => 'Cartridge pengganti dan replacement coil untuk berbagai device'
            ],
            [
                'nama' => 'Battery & Charger',
                'deskripsi' => 'Baterai eksternal 18650/21700 dan charger universal'
            ],
            [
                'nama' => 'Aksesoris Vape',
                'deskripsi' => 'Cotton, wire, drip tip, case, dan perlengkapan vaping'
            ],
            [
                'nama' => 'Atomizer & Tank',
                'deskripsi' => 'RTA, RDA, RDTA, dan sub-ohm tank untuk advanced user'
            ],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }

        $this->command->info('✅ Berhasil membuat ' . count($kategoris) . ' kategori vape store!');
    }
}
