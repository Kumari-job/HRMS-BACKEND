<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeBankRequest;
use App\Models\EmployeeBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeBankController extends Controller
{
    public function store(EmployeeBankRequest $request)
    {
        try {
            if (EmployeeBank::where('employee_id', $request->employee_id)->where('is_primary', 1)->exists() && $request->is_primary == 1) {
                return response()->json(['error' => true, 'message' => 'Employee can have only one primary bank'], 403);
            }
            $employeeBank = new EmployeeBank($request->all());
            $employeeBank->created_by = Auth::id();
            $employeeBank->save();
            return response()->json(['success' => true, 'message' => 'Employee bank detail added successfully'], 201);
        }catch (\Exception $exception){
            Log::error('Unable to store employee bank detail: '.$exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to store employee bank detail'], 400);
        }
    }
    public function update(EmployeeBankRequest $request, $id)
    {
        try {
            $employeeBank = EmployeeBank::find($id);

            if (!$employeeBank) {
                return response()->json(['error' => true, 'message' => 'Employee bank detail not found'], 404);
            }
            if (EmployeeBank::where('employee_id', $request->employee_id)
                    ->where('is_primary', 1)
                    ->where('id', '!=', $id)
                    ->exists() && $request->is_primary == 1) {
                return response()->json(['error' => true, 'message' => 'Employee can have only one primary bank'], 403);
            }
            $employeeBank->updated_by = Auth::id();
            $employeeBank->update($request->all());

            return response()->json(['success' => true, 'message' => 'Employee bank detail updated successfully'], 200);
        } catch (\Exception $exception) {
            Log::error('Unable to update employee bank detail: ' . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to update employee bank detail'], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $employeeBank = EmployeeBank::findOrFail($id);
            if (!$employeeBank) {
                return response()->json(['error' => true, 'message' => 'Employee bank detail not found'], 404);
            }
            $employeeBank->delete();

            return response()->json(['success' => true, 'message' => 'Employee bank detail deleted successfully'], 200);
        }  catch (\Exception $exception) {
            Log::error('Unable to delete employee bank detail: ' . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to delete employee bank detail'], 400);
        }
    }

}
