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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->boolean('is_present');
            $table->date('date');
            $table->time('punch_in_at');
            $table->string('punch_in_ip');
            $table->integer('late_punch_in');
            $table->time('punch_out_at')->nullable();
            $table->string('punch_out_ip')->nullable();
            $table->boolean('remark')->nullable();
            $table->boolean('is_approved')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('employee_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
