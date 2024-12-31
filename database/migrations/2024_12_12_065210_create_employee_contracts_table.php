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
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('contract_type');
            $table->string('job_description');
            $table->decimal('gross_salary')->nullable();
            $table->decimal('basic_salary')->nullable();
            $table->decimal('pf_from_employee')->nullable();
            $table->decimal('pf_from_company')->nullable();
            $table->decimal('gratuity')->nullable();
            $table->decimal('cit_percentage')->nullable();
            $table->decimal('cit_amount')->nullable();
            $table->decimal('ssf_amount')->nullable();
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('employee_contracts');
    }
};
