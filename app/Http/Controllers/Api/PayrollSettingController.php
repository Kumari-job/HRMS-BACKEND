<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PayrollSettingRequest;
use App\Http\Resources\PayrollSettingResource;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PayrollSettingController extends Controller
{
    public function showPayrollSetting()
    {
        $companyProfile = PayrollSetting::first();
        return new PayrollSettingResource($companyProfile);
    }
    public function upsert(PayrollSettingRequest $request)
    {
        try{
            $company_id = Auth::user()->selectedCompany->company_id;
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            if($request->filled('vat_number')){
                $data['pan_number'] = null ;
            }else{
                $data['vat_number'] = null ;
            }
            
            $payrollSetting = new PayrollSetting();
            $payrollSetting->updateOrCreate(['company_id' => $company_id], $data);
            return response()->json(['success' => true,'message' => 'Payroll Setting updated successfully.'],200);
        }catch (\Exception $e){
            Log::error("Unable to update payroll setting". $e->getMessage());
            return response()->json(['error' => true,'message' => "Unable to update payroll setting"],500);
        }
    }
}
