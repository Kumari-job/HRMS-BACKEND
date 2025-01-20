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
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->unsignedBigInteger('employee_id')->nullable()->after('idp_user_id');
            $table->boolean('is_password_changed')->default(1)->after('employee_id');
            $table->dateTime('last_login_at')->nullable()->after('is_password_changed');
            $table->string('otp')->nullable()->after('last_login_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->string('token')->nullable()->after('otp_expires_at');
            $table->rememberToken();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password');
            $table->dropForeign(['employee_id']);
            $table->dropColumn('last_login_at');
            $table->dropColumn('employee_id');
            $table->dropColumn('is_password_changed');
            $table->dropColumn('otp_expires_at');
            $table->dropColumn('token');
            $table->dropColumn('otp');
            $table->dropColumn('remember_token');
        });
    }
};
