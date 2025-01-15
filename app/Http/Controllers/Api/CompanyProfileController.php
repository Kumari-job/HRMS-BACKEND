<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyProfileRequest;
use App\Http\Resources\CompanyProfileResource;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanyProfileController extends Controller
{
    public function showCompanyProfile()
    {
        $companyProfile = CompanyProfile::first();
        return new CompanyProfileResource($companyProfile);
    }

    public function upsert(CompanyProfileRequest $request)
    {
        try {
            $company_id = Auth::user()->selectedCompany->company_id;
            $data = $request->validated();
            $companyProfile = new CompanyProfile();
            $companyProfile->updateOrCreate([
                'company_id' => $company_id,
            ], [
                'fiscal_calendar_type' => $data['fiscal_calendar_type'],
                'fiscal_start_month' => $data['fiscal_start_month'],
                'week_start_day' => $data['week_start_day'],
                'week_end_day' => $data['week_end_day'],
                'weekly_leaves' => $data['weekly_leaves'],
                'country' => $data['country'],
            ]);
            return response()->json(['success' => true,'message'=> 'Company Profile updated Successfully.'],201);
        }catch (\Exception $exception){
            Log::error('Unable to update Company Profile: '.$exception->getMessage());
            return response()->json(['success' => false,'message'=> 'Unable to update company profile.'], 500);
        }

    }
    public function showCompanyCountryPolicy()
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $companyProfile = CompanyProfile::select('country')->where('company_id', $company_id)->first();
        $country = strtolower($companyProfile->country);
        $jsonFile = public_path('assets/country_policy/'.$country.'.json');

        if (file_exists($jsonFile)) {
            $content = json_decode(file_get_contents($jsonFile));
            return response()->json(['success' => true,'policy' => $content]);
        }
        return response()->json(['success' => false,'message'=> 'Country Policy not found.'], 404);
    }
}
