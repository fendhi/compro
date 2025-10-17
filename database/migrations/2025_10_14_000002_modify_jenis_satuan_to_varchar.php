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
            // Drop kolom ENUM yang lama
            $table->dropColumn(['jenis_barang', 'satuan']);
        });
        
        Schema::table('barangs', function (Blueprint $table) {
            // Tambah kolom VARCHAR untuk jenis_barang (bebas input)
            $table->string('jenis_barang')->default('eceran')->after('kategori_id');
            
            // Tambah kolom satuan fixed 'pcs'
            $table->string('satuan', 10)->default('pcs')->after('harga');
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
