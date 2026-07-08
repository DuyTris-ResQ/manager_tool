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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->nullable()->constrained('licenses')->onDelete('set null');
            $table->string('device_id')->unique();
            $table->string('computer_name');
            $table->string('cpu')->nullable();
            $table->string('gpu')->nullable();
            $table->string('os')->nullable();
            $table->string('ip')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamp('first_login')->nullable();
            $table->timestamp('last_online')->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
