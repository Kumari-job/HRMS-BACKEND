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
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('citizenship_front_image');
            $table->dropColumn('citizenship_number');
            $table->dropColumn('citizenship_back_image');
            $table->dropColumn('pan_number');
            $table->string('marital_status')->after('date_of_birth');
            $table->string('blood_group')->after('marital_status');
            $table->string('religion')->after('blood_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('marital_status');
            $table->dropColumn('blood_group');
            $table->dropColumn('religion');
            $table->string('citizenship_front_image');
            $table->string('citizenship_number');
            $table->string('citizenship_back_image');
            $table->string('pan_number');
        });
    }
};
