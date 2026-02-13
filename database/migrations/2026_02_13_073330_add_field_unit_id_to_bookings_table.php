<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('field_unit_id')->nullable()->after('sports_field_id')
                ->constrained('field_units')->nullOnDelete();

            // ดัชนีใหม่สำหรับกันชนกันเร็วขึ้น
            $table->index(['field_unit_id', 'date'], 'idx_unit_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};