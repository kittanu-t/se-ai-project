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
    public function up()
    {
        Schema::table('sports_fields', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('field_units', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('sports_fields', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('field_units', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};