<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->text('comment');           // ข้อความรีวิว
            $table->integer('rating');         // คะแนน 1-5
            $table->string('sentiment_label')->nullable(); // AI: positive/negative
            $table->float('sentiment_score')->nullable(); // AI: ความมั่นใจ
            $table->timestamps();
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
