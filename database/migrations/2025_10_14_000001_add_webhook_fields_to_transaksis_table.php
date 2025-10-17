<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Order ID untuk reference di webhook (nullable first)
            $table->string('order_id')->nullable()->after('id');
            
            // Payment status tracking  
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])
                  ->default('paid') // Default paid untuk data existing
                  ->after('total');
            
            // Payment verification flag
            $table->boolean('payment_verified')->default(true)->after('payment_status'); // Default true untuk existing
            
            // External transaction ID dari BCA
            $table->string('external_transaction_id')->nullable()->after('payment_verified');
            
            // Timestamp pembayaran diterima
            $table->timestamp('paid_at')->nullable()->after('external_transaction_id');
            
            // Notes untuk kasus khusus (misal: amount mismatch)
            $table->text('payment_notes')->nullable()->after('paid_at');
        });

        // Generate order_id untuk data existing
        DB::statement("
            UPDATE transaksis 
            SET order_id = CONCAT('ORDER-', DATE_FORMAT(created_at, '%Y%m%d%H%i%s'), '-', id),
                paid_at = created_at
            WHERE order_id IS NULL
        ");

        // Sekarang buat unique constraint setelah order_id terisi
        Schema::table('transaksis', function (Blueprint $table) {
            $table->unique('order_id');
            
            // Index untuk performance
            $table->index('payment_status');
            $table->index(['payment_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex(['transaksis_order_id_index']);
            $table->dropIndex(['transaksis_payment_status_index']);
            $table->dropIndex(['transaksis_payment_status_created_at_index']);
            
            $table->dropColumn([
                'order_id',
                'payment_status',
                'payment_verified',
                'external_transaction_id',
                'paid_at',
                'payment_notes'
            ]);
        });
    }
};
