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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->string('password')->nullable();
            $table->string('email');
            $table->string('image')->nullable();
            $table->string('mobile');
            $table->string('gender');
            $table->string('address');
            $table->string('date_of_birth');
            $table->string('citizenship_number');
            $table->string('citizenship_front_image')->nullable();
            $table->string('citizenship_back_image')->nullable();
            $table->string('pan_number')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
