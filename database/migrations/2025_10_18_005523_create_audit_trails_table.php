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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable(); // Backup if user deleted
            $table->string('module'); // e.g., 'purchase', 'barang', 'transaksi'
            $table->string('action'); // e.g., 'create', 'update', 'delete', 'approve', 'reject'
            $table->string('description'); // Human readable description
            $table->string('model_type')->nullable(); // Model class name
            $table->unsignedBigInteger('model_id')->nullable(); // Model ID
            $table->json('old_values')->nullable(); // Before update
            $table->json('new_values')->nullable(); // After update
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('module');
            $table->index('action');
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
