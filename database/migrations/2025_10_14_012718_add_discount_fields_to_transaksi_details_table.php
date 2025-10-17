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
        Schema::table('transaksi_details', function (Blueprint $table) {
            $table->decimal('diskon_persen', 5, 2)->default(0)->after('harga');
            $table->decimal('diskon_amount', 10, 2)->default(0)->after('diskon_persen');
            $table->decimal('harga_setelah_diskon', 10, 2)->after('diskon_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_details', function (Blueprint $table) {
            $table->dropColumn(['diskon_persen', 'diskon_amount', 'harga_setelah_diskon']);
        });
    }
};
