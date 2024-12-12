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
        Schema::create('employee_addresses', function (Blueprint $table) {
            $table->id();
//          Permanent Address
            $table->unsignedBigInteger('employee_id');
            $table->string('p_country')->nullable();
            $table->string('p_district')->nullable();
            $table->string('p_vdc_or_municipality')->nullable();
            $table->string('p_ward')->nullable();
            $table->string('p_state')->nullable();
            $table->string('p_street')->nullable();
            $table->string('p_house_number')->nullable();
            $table->string('p_zip_code')->nullable();
//          Temporary Address
            $table->string('t_country')->nullable();
            $table->string('t_district')->nullable();
            $table->string('t_vdc_or_municipality')->nullable();
            $table->string('t_ward')->nullable();
            $table->string('t_state')->nullable();
            $table->string('t_street')->nullable();
            $table->string('t_house_number')->nullable();
            $table->string('t_zip_code')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_addresses');
    }
};
