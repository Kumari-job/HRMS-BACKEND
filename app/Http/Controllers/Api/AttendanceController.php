<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\AttendanceUpdateRequest;
use App\Models\Attendance;
use App\Models\CompanyProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if (in_array($key, ['id', 'company_id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }
        $attendance = $query->orderByDesc('date')->paginate($request->page_size ?? 10);
        return response()->json($attendance);
    }
    public function store(AttendanceRequest $request){
        try {
            $data = $request->except('date','date_nepali');
            $date  = $request->date_nepali ? DateHelper::nepaliToEnglish($request->date_nepali) : $request->date ?? now()->format('Y-m-d');
            DB::beginTransaction();
            foreach ($data['attendance'] as $item) {
                $attendance = Attendance::updateOrCreate([
                    'date' => $date,
                    'employee_id' => $item['employee_id'],
                ],[
                    'status' => $item['status'],
                    ]
                );
            }
            DB::commit();
            return response()->json(['success'=>true,'message'=>'Attendance added successfully.'],201);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error("Unable to store attendance: ".$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to store attendance'],500);
        }
    }
    public function punchIn(Request $request)
    {
        try {
            $user = Auth::user();

            $date =  Carbon::now()->toDateString();

            if (Attendance::where('date', $date)->where('employee_id',$user->id)->exists()) {

                return response()->json(['error'=>true,'message'=>'Attendance already exists.'],403);
            }
            $ip = $request->ip();
            $company_profile = CompanyProfile::where('company_id', $user->selectedCompany->company_id)->first();
            $startTime = Carbon::createFromFormat('H:i:s', $company_profile->start_time);
            $nowTime = Carbon::now()->toTimeString();
            $now = Carbon::createFromFormat('H:i:s', $nowTime);

            $minutesDifference = max($startTime->diffInMinutes($now, false), 0);

            $attendance = new Attendance();
            $attendance->employee_id = $user->id;
            $attendance->is_present = true;
            $attendance->date = $date;
            $attendance->punch_in_at = Carbon::now()->toTimeString();
            $attendance->punch_in_ip = $request->ip();
            $attendance->late_punch_in = $minutesDifference;
            $attendance->created_by = $user->id;
            $attendance->save();
            return response()->json(['success' => true, 'message' => 'Attendance added successfully.'], 201);
        }catch (\Exception $exception){
            Log::error("Unable to store attendance: ".$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to store attendance'],500);
        }
    }
    public function punchOut(Request $request)
    {

    }
}
