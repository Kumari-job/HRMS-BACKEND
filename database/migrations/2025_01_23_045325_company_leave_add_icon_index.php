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
        Schema::table('company_leaves' , function (Blueprint $table){
            $table->integer('icon_index')->nullable()->after('exclude_weekend');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_leaves' , function (Blueprint $table) {
            $table->dropColumn('icon_index');
        });
    }
};
