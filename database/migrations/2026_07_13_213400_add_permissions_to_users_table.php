<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('settings');
            $table->boolean('is_active')->default(true)->after('permissions');
            $table->integer('max_licenses')->default(0)->after('is_active'); // 0 = unlimited
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['permissions', 'is_active', 'max_licenses']);
        });
    }
};
