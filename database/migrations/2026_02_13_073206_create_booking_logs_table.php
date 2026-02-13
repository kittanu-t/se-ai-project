<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->enum('action', ['created','updated','approved','rejected','cancelled','completed']);
            $table->foreignId('by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent(); // audit log มักไม่ต้อง updated_at
        });
    }
    public function down(): void {
        Schema::dropIfExists('booking_logs');
    }
};