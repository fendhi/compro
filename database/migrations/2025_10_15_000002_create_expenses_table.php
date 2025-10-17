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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedBigInteger('kategori_id');
            $table->string('deskripsi');
            $table->decimal('nominal', 15, 2);
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'qris', 'ewallet'])->default('cash');
            $table->string('bukti_path')->nullable()->comment('Path foto bukti');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('user_id')->comment('User yang input');
            $table->timestamps();

            $table->foreign('kategori_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('tanggal');
            $table->index('kategori_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
