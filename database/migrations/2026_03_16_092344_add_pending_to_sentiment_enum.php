<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE reviews MODIFY COLUMN sentiment ENUM('positive', 'negative', 'neutral', 'pending') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE reviews MODIFY COLUMN sentiment ENUM('positive', 'negative', 'neutral') NOT NULL DEFAULT 'neutral'");
    }
};