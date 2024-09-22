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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('password'); // Profile picture
            $table->string('mobile_no')->nullable()->after('profile_image'); // Mobile number
            $table->foreignId('role_id')->constrained('roles')->default(4)->after('mobile_no'); // Role ID (default to Free-user)
            $table->string('timezone')->nullable()->after('role_id'); // Timezone
            $table->enum('status', ['active', 'deactive', 'blacklisted'])->default('active')->after('timezone'); // Status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
