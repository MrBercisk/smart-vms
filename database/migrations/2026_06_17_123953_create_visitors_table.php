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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_code')->unique();
            $table->string('full_name');
            $table->string('company_name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('identity_number');
            $table->enum('identity_type', ['KTP', 'SIM', 'Passport']);
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'blacklist'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
