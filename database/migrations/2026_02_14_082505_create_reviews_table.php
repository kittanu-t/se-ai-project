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
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();

        $table->foreignId('booking_id')
            ->constrained()
            ->cascadeOnDelete()
            ->unique();

        $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('sports_field_id')
            ->constrained('sports_fields')
            ->cascadeOnDelete();

        $table->unsignedTinyInteger('rating'); // 1–5
        $table->text('comment');

        $table->enum('sentiment', ['positive','neutral','negative']);
        $table->decimal('confidence_score', 5, 4);

        $table->timestamps();

        $table->index('sports_field_id');
        $table->index('sentiment');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
