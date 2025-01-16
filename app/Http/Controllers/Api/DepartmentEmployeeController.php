<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentEmployeeRequest;
use App\Http\Resources\DepartmentEmployeeResource;
use App\Models\Department;
use App\Models\DepartmentEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DepartmentEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function employeesByDepartment(Request $request, $id)
    {
        $query = DepartmentEmployee::with(['employee' => function ($query) {
            $query->whereNull('deleted_at');
        }, 'department', 'createdBy', 'updatedBy'])->where('department_id', $id);

        $departmentEmployees = $query->latest()->paginate($request->page_size ?? 10);
        return DepartmentEmployeeResource::collection($departmentEmployees);
    }

    public function employeesByBranch(Request $request, $branchID)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $departmentIDs = Department::whereHas('branch', function ($q) use ($branchID, $company_id) {
            $q->where('id', $branchID)->where('company_id', $company_id);
        })->pluck('id')->toArray();
        $query = DepartmentEmployee::with('employee', 'department', 'createdBy', 'updatedBy')->whereIn('department_id', $departmentIDs);

        $departmentEmployees = $query->latest()->paginate($request->page_size ?? 10);
        return DepartmentEmployeeResource::collection($departmentEmployees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentEmployeeRequest $request)
    {
        try {
            $data = $request->except('joined_at');
            $department = Department::select('branch_id')->find($data['department_id']);
            if (!$department) {
                return response()->json(['error'=>true,'message' => 'Department not found'], 404);
            }
            if(DepartmentEmployee::whereHas('department', function ($q) use ($department) {
                $q->where('branch_id','!=', $department->branch_id);
            })->where('employee_id', $data['employee_id'])->exists()) {
                return response()->json(['error'=>true,'message'=>'Employee exists in another branch'],422);
            }
            $joined_at = $request->filled('joined_at_nepali') ? DateHelper::nepaliToEnglish($request->joined_at_nepali) : $request->joined_at;
            $data['joined_at'] = $joined_at;
            $departmentEmployee = new DepartmentEmployee();
            $departmentEmployee->fill($data);
            $departmentEmployee->created_by = Auth::id();
            $departmentEmployee->save();
            return response()->json(['success' => true, 'message' => 'Department employee added successfully.'], 201);
        } catch (\Exception $exception) {
            Log::error("Unable to add department employee: " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to add department employee.'], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentEmployeeRequest $request, string $id)
    {
        try {
            $departmentEmployee = DepartmentEmployee::find($id);
            if (!$departmentEmployee) {
                return response()->json(['error' => true, 'message' => 'Department employee not found.'], 404);
            }
            $data = $request->except('joined_at');
            $department = Department::select('branch_id')->find($data['department_id']);

            if(DepartmentEmployee::where('id', '!=', $id)->whereHas('department', function ($q) use ($department) {
                $q->where('branch_id','!=', $department->branch_id);
            })->where('employee_id', $data['employee_id'])->exists()) {
                return response()->json(['error'=>true,'message'=>'Employee exists in another branch'],422);
            }
            $joined_at = $request->filled('joined_at_nepali') ? DateHelper::nepaliToEnglish($request->joined_at_nepali) : $request->joined_at;
            $data['joined_at'] = $joined_at;
            $data['updated_by'] = Auth::id();
            $departmentEmployee->update($data);
            return response()->json(['success' => true, 'message' => 'Department employee updated successfully.'], 200);
        } catch (\Exception $exception) {
            Log::error("Unable to update department employee: " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to update department employee.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        $ids = $request->ids;
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $departmentEmployees = DepartmentEmployee::whereIn('id', $ids);
        $count = $departmentEmployees->count();
        if ($count > 0) {

            $departmentEmployees->forceDelete();
            return response()->json(['success' => true, 'message' => 'Employees removed from department.'], 200);
        }
        return response()->json(['error' => true, 'message' => 'Department Employee not found.'], 200);
    }
}
