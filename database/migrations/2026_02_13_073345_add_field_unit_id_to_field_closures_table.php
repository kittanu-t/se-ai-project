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
        Schema::table('field_closures', function (Blueprint $table) {
            $table->foreignId('field_unit_id')->nullable()->after('sports_field_id')
                ->constrained('field_units')->nullOnDelete();
            $table->index(['field_unit_id','start_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_closures', function (Blueprint $table) {
            //
        });
    }
};