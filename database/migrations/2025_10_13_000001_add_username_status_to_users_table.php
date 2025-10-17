<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns without unique constraint first
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
        });
        
        // Generate username for existing users
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $username = strtolower(str_replace(' ', '', $user->name));
            $baseUsername = $username;
            $counter = 1;
            
            // Check if username exists and add number if needed
            while (DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
        
        // Now make username unique
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'status']);
        });
    }
};
