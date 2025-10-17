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
        Schema::table('transaksis', function (Blueprint $table) {
            // Check and add columns if they don't exist
            if (!Schema::hasColumn('transaksis', 'no_invoice')) {
                $table->string('no_invoice', 50)->unique()->after('id');
            }
            if (!Schema::hasColumn('transaksis', 'metode_pembayaran')) {
                $table->string('metode_pembayaran', 50)->after('diskon_amount');
            }
            if (!Schema::hasColumn('transaksis', 'bayar')) {
                $table->decimal('bayar', 15, 2)->default(0)->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('transaksis', 'kembalian')) {
                $table->decimal('kembalian', 15, 2)->default(0)->after('bayar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['no_invoice', 'metode_pembayaran', 'bayar', 'kembalian']);
        });
    }
};
