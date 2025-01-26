<?php

namespace App\Http\Controllers\Api;

use App\Events\Leave\EmployeeLeaveRequested;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeLeaveRequest;
use App\Http\Resources\EmployeeLeaveResource;
use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\error;

class EmployeeLeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeLeave::with([
            'leaveStatus' => function ($query) {
                $query
                    ->orderBy('status', 'ASC');
            },
            'leaveStatus.requestedTo',
            'requestedBy',
            'leave' => function ($query) {
                $query
                    ->select('name','id');
            }
            ,
        ])
            // ->whereHas('leaveStatus', function ($q) {
            //     $q->whereIn('status', [null, 0]);
            // })


            // Additional filtering based on request parameters
            ->where(function ($q) use ($request) {
                foreach ($request->except('page', 'page_size') as $key => $value) {
                    if (isset($value) && !empty($value)) {
                        if (in_array($key, ['id', 'leave_id'])) {
                            $q->where($key, $value);
                        } else {
                            $q->where($key, 'LIKE', '%' . $value . '%');
                        }
                    }
                }
            });

        $employee_leaves = $query->latest()
            ->paginate($request->page_size ?? 10);
        return EmployeeLeaveResource::collection($employee_leaves);
    }
    public function showUsersLeaves(Request $request)
    {
        $query = EmployeeLeave::with('leaveStatus.requestedTo:id,idp_user_id,name,image_path,employee_id','leave:name,company_id,id');

        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if (in_array($key, ['id', 'leave_id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }
        $employee_leaves = $query->where('requested_by',Auth::id())->latest()->paginate($request->page_size ?? 10);
        return EmployeeLeaveResource::collection($employee_leaves);
    }
    public function store(EmployeeLeaveRequest $request)
    {
        try{
            $data = $request->except('requested_to','start_date','end_date');
            $data['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $data['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            if ($request->leave_type != 'full')
            {
                $data['start_time'] = $request->start_date ? Carbon::parse($request->start_date)->format('H:i:s') : null;
                $data['end_time'] = $request->end_date ? Carbon::parse($request->end_date)->format('H:i:s') : null;
            }
            $employee_leave = new EmployeeLeave();
            $employee_leave->fill($data);
            $employee_leave->requested_by = Auth::id();
            $employee_leave->save();
            $employee_id = Auth::user()->employee_id;
            if ($employee_leave) {
                EmployeeLeaveRequested::dispatch($employee_leave,$employee_id,$request->requested_to);
                $employee_leave_statuses = collect($request->requested_to)->map(function ($requested_to) use ($employee_leave) {
                    return [
                        'requested_to' => $requested_to,
                        'employee_leave_id' => $employee_leave->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                EmployeeLeaveStatus::insert($employee_leave_statuses);

            }
            return response()->json(['success'=>true,'message' => 'Leave added successfully.'], 201);
        }catch (\Exception $e){
            Log::error("Unable to store employee leave: ". $e->getMessage());
            return response(['error'=>true, 'message' => "Unable to store employee leave"], 500);
        }
    }
    public function update(EmployeeLeaveRequest $request, $id)
    {
        $employee_leave = EmployeeLeave::find($id);
        if (!$employee_leave) {
            return response(['error'=>true,'message' => 'Employee Leave not found'], 404);
        }
        if(EmployeeLeaveStatus::where('employee_leave_id',$employee_leave->id)->whereNotNull('status')->exists()){
            return response(['error'=>true,'message' => 'The decision for the leave is already done'], 409);
        }

    }
}
