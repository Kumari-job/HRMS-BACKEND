<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyHolidayRequest;
use App\Http\Resources\CompanyHolidayResource;
use App\Models\CompanyHoliday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompanyHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CompanyHoliday::with('createdBy','updatedBy');

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

        $companyHolidays = $query->latest()->paginate($request->page_size ?? 10);
        return CompanyHolidayResource::collection($companyHolidays);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyHolidayRequest $request)
    {
        try{
            $company_id = Auth::user()->selectedCompany->company_id;
            $data = $request->except('date','date_nepali');

            $date =  $request->filled('date_nepali') ? DateHelper::nepaliToEnglish($request->date_nepali) : $request->date;
            $data['date'] = $date;
            $companyHoliday = new CompanyHoliday();
            $companyHoliday->fill($data);
            $companyHoliday->company_id = $company_id;
            $companyHoliday->created_by = Auth::id();
            $companyHoliday->save();
            return response()->json(['success'=>true,'message'=>'Holiday added successfully'],200);
        }catch (\Exception $exception){
            Log::error("Unable to create company holiday: ". $exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to create holiday'],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $companyHoliday = CompanyHoliday::with('createdBy','updatedBy')->find($id);
        if (!$companyHoliday) {
            return response()->json(['error'=>true,'message'=>'Holiday not found'],404);
        }
        return new CompanyHolidayResource($companyHoliday);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyHolidayRequest $request, string $id)
    {
        try {
            $data = $request->except('date', 'date_nepali');

            $date = $request->filled('date_nepali') ? DateHelper::nepaliToEnglish($request->date_nepali) : $request->date;
            $data['date'] = $date;

            $companyHoliday = CompanyHoliday::where('id', $id)->first();
            if (!$companyHoliday) {
                return response()->json(['error' => true, 'message' => 'Holiday not found'], 404);
            }

            $companyHoliday->fill($data);
            $companyHoliday->updated_by = Auth::id();
            $companyHoliday->update();

            return response()->json(['success' => true, 'message' => 'Holiday updated successfully.'], 200);
        }catch (\Exception $exception){
            Log::error("Unable to update holiday: ". $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to update holiday'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'ids' => 'array'
            ]);
            $ids = $request->ids;
            if ($validator->fails()) {
                return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
            }
            $company_holidays = CompanyHoliday::whereIn('id', $ids);
            $count = $company_holidays->count();
            if ($count > 0) {
                $deleteStatus = $company_holidays->delete();

                return response()->json(['success' => true, 'message' => 'Holidays deleted successfully.'], 200);
            }
            return response()->json(['error' => true, 'message' => 'Holidays not found.'], 400);
        } catch (\Exception $exception) {
            Log::error("Unable to delete holidays: ". $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to delete holidays'], 500);
        }
    }
}
