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
            $table->enum('diskon_type', ['none', 'percentage', 'nominal'])->default('none')->after('total');
            $table->decimal('diskon_value', 10, 2)->default(0)->after('diskon_type');
            $table->decimal('diskon_amount', 10, 2)->default(0)->after('diskon_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['diskon_type', 'diskon_value', 'diskon_amount']);
        });
    }
};
