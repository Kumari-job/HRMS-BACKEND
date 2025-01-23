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
        Schema::table('asset_sales', function (Blueprint $table) {
            $table->date('sold_at')->nullable()->after('sold_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_sales', function (Blueprint $table) {
            $table->dropColumn('sold_at');
        });
    }
};
