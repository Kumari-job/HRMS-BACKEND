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
        //
        Schema::table('asset_usages' , function (Blueprint $table) {
            $table->boolean('usage_status')->after('assigned_end_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('asset_usages', function (Blueprint $table) {
            $table->dropColumn('usage_status');
        });
    }
};
