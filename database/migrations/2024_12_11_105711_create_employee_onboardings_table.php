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
        Schema::create('employee_onboardings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('shortlisted_at')->nullable();
            $table->date('interviewed_at')->nullable();
            $table->date('offered_at')->nullable();
            $table->longText('offer_letter')->nullable();
            $table->unsignedBigInteger('offered_by')->nullable();
            $table->date('joined_at')->nullable();
            $table->string('employment_type')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('offered_by')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_onboardings');
    }
};
