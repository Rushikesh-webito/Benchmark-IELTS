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
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('name')->unique(); // Role name
            $table->timestamps(); // Created and updated timestamps
        });

        // Optional: Seed initial roles
        DB::table('roles')->insert([
            ['name' => 'Super Admin'],
            ['name' => 'Admin'],
            ['name' => 'Teachers'],
            ['name' => 'Free-user'],
            ['name' => 'Premium-user'],
        ]);
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
