<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AI PK

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sports_field_id')->constrained('sports_fields')->cascadeOnDelete();

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', ['pending','approved','rejected','cancelled','completed'])
                  ->default('pending');

            $table->text('purpose')->nullable();
            $table->string('contact_phone', 30)->nullable();

            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->dateTime('approved_at')->nullable();
            $table->string('cancel_reason', 255)->nullable();

            $table->timestamps();

            // Composite Indexes
            $table->index(['sports_field_id', 'date'], 'idx_field_date');
            $table->index(['user_id', 'date'], 'idx_user_date');
            $table->index('status', 'idx_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};