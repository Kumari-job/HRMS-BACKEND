<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\AttendanceUpdateRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\CompanyProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
    public function dailyAttendance(Request $request)
    {
        $attendance = Attendance::with('employee:id,name,image_path,mobile','createdBy:id,name,image_path,mobile')
            ->whereDate('date', '=', Carbon::today()->toDateString())
            ->where('employee_id', '=', Auth::id())->first();
        if (!$attendance) {
            return response()->json(['error'=>true,'message'=>'No attendance found'],404);
        }
        return new AttendanceResource($attendance);
    }
    public function store(AttendanceRequest $request){
        try {
            $data = $request->except('date','date_nepali','punch_in_at','punch_out_at');
            $date  = Carbon::parse($request->punch_in_at)->format('Y-m-d');
            if (Attendance::whereDate('date',$date)->where('employee_id',$request->employee_id)->exists()) {
                return response()->json(['error'=>true,'message'=>'Attendance already exists'],404);
            }
            $punch_in_at = $request->punch_in_at ? Carbon::parse($request->punch_in_at)->format('H:i:s') : null;
            $punch_out_at = $request->punch_out_at ? Carbon::parse($request->punch_out_at)->format('H:i:s') : null;
            $data['date'] = $date;
            $data['punch_in_at'] = $punch_in_at;
            $data['punch_out_at'] = $punch_out_at;
            $data['punch_in_ip'] = $request->ip();
            $data['punch_out_ip'] = $request->punch_out_at ? $request->ip() : null;
            $data['is_approved'] = true;
            $data['created_by'] = Auth::id();
            $company_profile = CompanyProfile::where('company_id', Auth::user()->selectedCompany->company_id)->first();
            $startTime = Carbon::createFromFormat('H:i:s', $company_profile->start_time);
            $minutesDifference = max($startTime->diffInMinutes($punch_in_at, false), 0);

            $data['late_punch_in'] = $minutesDifference;
            $attendance = new Attendance();
            $attendance->fill($data);
            $attendance->save();
            return response()->json(['success'=>true,'message'=>'Attendance added successfully.'],201);
        }catch (\Exception $exception){
            Log::error("Unable to store attendance: ".$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to store attendance'],500);
        }
    }

    public function update(AttendanceRequest $request, $id)
    {
        try{
            $attendance = Attendance::find($id);
            if (!$attendance) {
                return response()->json(['error'=>true,'message'=>'No attendance found'],404);
            }
            $data = $request->only('is_present');
            $punch_in_at = $request->punch_in_at ? Carbon::parse($request->punch_in_at)->format('H:i:s') : null;
            $punch_out_at = $request->punch_out_at ? Carbon::parse($request->punch_out_at)->format('H:i:s') : null;
            $data['punch_in_at'] = $punch_in_at;
            $data['punch_out_at'] = $punch_out_at;
            $data['punch_in_ip'] = $request->ip();
            $data['punch_out_ip'] = $punch_out_at ? $request->ip() : null;
            $company_profile = CompanyProfile::where('company_id', Auth::user()->selectedCompany->company_id)->first();
            $startTime = Carbon::createFromFormat('H:i:s', $company_profile->start_time);
            $minutesDifference = max($startTime->diffInMinutes($punch_in_at, false), 0);

            $data['late_punch_in'] = $minutesDifference;
            $attendance->fill($data);
            $attendance->update();
            return response()->json(['success'=>true,'message'=>'Attendance updated successfully.'],200);
        }catch (\Exception $exception){
            Log::error("Unable to update attendance: ".$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to update attendance'],500);
        }
    }
    public function punchIn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'remark' => 'nullable|string|max:125',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
            }
            $user = Auth::user();

            $date =  Carbon::now()->toDateString();

            if (Attendance::where('date', $date)->where('employee_id',$user->id)->exists()) {

                return response()->json(['error'=>true,'message'=>'Attendance already exists.'],403);
            }
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
            $attendance->remark= $request->remark;
            $attendance->punch_in_ip = $request->ip();
            $attendance->late_punch_in = $minutesDifference;
            $attendance->created_by = $user->id;
            $attendance->save();
            return response()->json(['success' => true, 'message' => 'Successfully punched in'], 201);
        }catch (\Exception $exception){
            Log::error("Unable to store attendance: ".$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to store attendance'],500);
        }
    }
    public function punchOut(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'remark' => 'nullable|string|max:125',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
            }
            $user = Auth::user();
            $date =  Carbon::now()->toDateString();
            $attendance = Attendance::where('date',$date)->where('employee_id',$user->id)->first();
            if (!$attendance) {
                return response()->json(['error'=>true,'message'=>'Attendance not found. Please punch in first'],403);
            }
            if (isset($attendance->punch_out_at)) {
                return response()->json(['error'=>true,'message'=>'User has already punched out'],403);

            }
            $attendance->punch_out_at = Carbon::now()->toTimeString();
            $attendance->punch_out_ip = $request->ip();
            $attendance->update();
            return response()->json(['success' => true, 'message' => 'Successfully punched out'], 201);
        }catch (\Exception $exception){
            Log::error("Unable to punch out: ".$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to punch out'],500);
        }
    }
}
