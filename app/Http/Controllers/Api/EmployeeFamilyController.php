<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DirectoryPathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeFamilyRequest;
use App\Http\Resources\EmployeeFamilyResource;
use App\Models\Employee;
use App\Models\EmployeeEducation;
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

    public function update(EmployeeFamilyRequest $request, $id)
    {
        try{
            $employeeFamily = EmployeeFamily::find($id);
            if (!$employeeFamily) {
                return response()->json(['error' => true,'message'=>'Employee Family not found'],404);
            }
            $employeeFamily->update($request->all());
            return response()->json(['success' => true,'message'=>'Employee family updated successfully'],200);
        } catch (\Exception $exception)
        {
            Log::error("Unable to update Employee family : {$exception->getMessage()}");
            return response()->json(['error' => true,'message'=>'Unable to update employee family'],400);
        }
    }

    public function destroy($id)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $employeeFamily = EmployeeFamily::find($id);
        if(!$employeeFamily){
            return response()->json(['error' => true,'message'=>'Family not found'],404);
        }

        $employeeFamily->delete();
        return response()->json(['success' => true,'message'=>'Family deleted successfully'],200);
    }
}
