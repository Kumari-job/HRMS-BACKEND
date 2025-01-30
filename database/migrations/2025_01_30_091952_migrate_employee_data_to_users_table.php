<?php

use App\Models\Employee;
use App\Models\SelectedCompany;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $employees = Employee::withoutGlobalScopes()->get();
            foreach ($employees as $employee) {
                if (!User::where('employee_id',$employee->id)->exists()) {
                    $user = new User();
                    $user->name = $employee->name;
                    $user->email = $employee->email;
                    $user->image_path = $employee->image_path;
                    $user->password = Hash::make('test@123');
                    $user->employee_id = $employee->id;
                    $user->is_password_changed = false;
                    $user->save();

                    if ($user)
                    {
                        $selectedCompany = new SelectedCompany();
                        $selectedCompany->company_id = $employee->company_id;
                        $selectedCompany->user_id = $user->id;
                        $selectedCompany->save();
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
