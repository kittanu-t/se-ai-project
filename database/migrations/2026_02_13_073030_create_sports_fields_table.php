<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sports_fields', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AI PK
            $table->string('name', 120);
            $table->string('sport_type', 60);
            $table->string('location', 200);
            $table->unsignedInteger('capacity')->default(0);
            $table->enum('status', ['available','closed','maintenance'])->default('available');

            $table->foreignId('owner_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // ถ้าเจ้าของถูกลบ ให้เป็น NULL

            $table->unsignedSmallInteger('min_duration_minutes')->default(60);
            $table->unsignedSmallInteger('max_duration_minutes')->default(180);
            $table->unsignedSmallInteger('lead_time_hours')->default(1);

            $table->timestamps();

            // Indexes
            $table->index('sport_type');
            $table->index('status');
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sports_fields');
    }
};