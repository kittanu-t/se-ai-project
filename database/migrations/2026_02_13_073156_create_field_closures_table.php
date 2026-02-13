<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('field_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sports_field_id')->constrained('sports_fields')->cascadeOnDelete();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('reason', 255)->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['sports_field_id', 'start_datetime'], 'idx_field_start');
        });
    }
    public function down(): void {
        Schema::dropIfExists('field_closures');
    }
};