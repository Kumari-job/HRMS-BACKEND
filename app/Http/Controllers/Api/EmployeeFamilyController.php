<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeFamilyRequest;
use App\Http\Resources\EmployeeFamilyResource;
use App\Models\Employee;
use App\Models\EmployeeFamily;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeFamilyController extends Controller
{
    public function index($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        $query = EmployeeFamily::query();

        $employee_families = $query->where('employee_id',$employee_id)->latest()->get();
        return EmployeeFamilyResource::collection($employee_families);
    }
    public function store(EmployeeFamilyRequest $request)
    {
        try {
            $employeeFamily = new EmployeeFamily($request->all());
            $employeeFamily->created_by = Auth::id();
            $employeeFamily->save();
            return response()->json(['success' => true,'message'=>'Employee family entered successfully'],201);
        } catch (\Exception $exception)
        {
            Log::error("Unable to store Employee family : {$exception->getMessage()}");
            return response()->json(['error' => true,'message'=>'Unable to store employee family'],400);
        }
    }
}
