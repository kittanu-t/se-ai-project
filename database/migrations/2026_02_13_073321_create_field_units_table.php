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
        Schema::create('field_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sports_field_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);              // เช่น Court 1, Table 3, Lane B
            $table->unsignedSmallInteger('index')->default(1); // ลำดับสำหรับ sort
            $table->enum('status', ['available','closed','maintenance'])->default('available');
            $table->unsignedInteger('capacity')->default(0); // ส่วนใหญ่ = 1
            $table->timestamps();

            $table->index(['sports_field_id', 'index']);
            $table->index(['sports_field_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_units');
    }
};