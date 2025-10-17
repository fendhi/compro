<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Tambah kolom jenis_barang
            $table->enum('jenis_barang', [
                'eceran',
                'grosir',
                'konsinyasi'
            ])->default('eceran')->after('kategori_id');
            
            // Tambah kolom satuan
            $table->enum('satuan', [
                // Unit/Pieces
                'pcs',
                'unit',
                'set',
                'pasang',
                // Package
                'box',
                'pack',
                'karton',
                'dos',
                'lusin',
                'kodi',
                'gross',
                'rim',
                // Weight
                'kg',
                'gram',
                'ons',
                'ton',
                'kwintal',
                // Volume
                'liter',
                'ml',
                'galon',
                // Length
                'meter',
                'cm',
                'roll',
                'yard',
                // Food/Portion
                'porsi',
                'cup',
                'botol',
                'kaleng'
            ])->default('pcs')->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['jenis_barang', 'satuan']);
        });
    }
};
