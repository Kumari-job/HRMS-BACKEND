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
        Schema::create('employee_leave_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_leave_id');
            $table->unsignedBigInteger('requested_to');
            $table->string('status')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();

            $table->foreign('employee_leave_id')->references('id')->on('employee_leaves')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_statuses');
    }
};
