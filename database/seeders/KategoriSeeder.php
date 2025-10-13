<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            'Makanan',
            'Minuman',
            'Elektronik',
            'Pakaian',
            'Alat Tulis',
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create(['nama' => $kategori]);
        }
    }
}
