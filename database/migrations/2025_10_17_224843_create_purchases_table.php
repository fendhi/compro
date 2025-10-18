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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('restrict');
            $table->date('tanggal_po');
            $table->date('tanggal_terima')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'ordered', 'partial', 'completed', 'cancelled'])->default('draft');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            
            // Tracking users
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('restrict');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('status');
            $table->index('tanggal_po');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
