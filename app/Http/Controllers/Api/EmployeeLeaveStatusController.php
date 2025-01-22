<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeLeaveStatusRequest;
use App\Models\EmployeeLeaveStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeLeaveStatusController extends Controller
{
    public function listPendingLeaves(Request $request)
    {
        $employee_leave_statuses = EmployeeLeaveStatus::whereNull('status')->get()->groupBy('employee_leave_id');
        return response()->json($employee_leave_statuses);
    }

    public function changeStatus(EmployeeLeaveStatusRequest $request, $employee_leave_status_id)
    {
        try {
            $employee_leave_status = EmployeeLeaveStatus::where('requested_to', Auth::id())->find($employee_leave_status_id);
            if (!$employee_leave_status) {
                return response()->json(['error' => true, 'message' => 'Employee Leave not found'], 404);
            }
            if (EmployeeLeaveStatus::where('employee_leave_id', $employee_leave_status->employee_leave_id)->where('status','rejected')->exists()) {
                return response()->json(['error' => true, 'message' => 'Leave has already been rejected'], 409);
            }
            $employee_leave_status->status = $request->status;
            $employee_leave_status->update();
            return response()->json(['success' => true,"message" => "Leave has been ".$employee_leave_status->status], 200);
        }catch (\Exception $exception){
            Log::error("Unable to update Employee Leave Status: " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to update employee leave'], 500);
        }

    }
}
