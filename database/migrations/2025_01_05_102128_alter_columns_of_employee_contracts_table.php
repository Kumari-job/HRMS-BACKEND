<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->dropColumn('ssf_amount');
            $table->dropColumn('cit_percentage');

            $table->decimal('extra_pf_from_employee')->unsigned()->nullable()->after('pf_from_employee');
            $table->decimal('ssf_from_employee')->unsigned()->nullable()->after('pf_from_company');
            $table->decimal('extra_ssf_from_employee')->unsigned()->nullable()->after('ssf_from_employee');
            $table->decimal('ssf_from_company')->unsigned()->nullable()->after('extra_ssf_from_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->decimal('ssf_amount')->nullable();
            $table->decimal('cit_percentage')->nullable();
            $table->dropColumn('extra_pf_from_employee');
            $table->dropColumn('ssf_from_employee');
            $table->dropColumn('ssf_from_company');
            $table->dropColumn('extra_ssf_from_employee');
        });
    }
};
