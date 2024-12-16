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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_category_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('cost', 8, 2);
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchased_at');
            $table->date('warranty_end_at')->nullable();
            $table->string('warranty_image')->nullable();
            $table->string('guarantee_end_at')->nullable();
            $table->string('guarantee_image')->nullable();
            $table->string('status');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
