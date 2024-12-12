<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DirectoryPathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeExperienceRequest;
use App\Models\EmployeeExperience;
use App\Traits\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeExperienceController extends Controller
{
    use FileHelper;
    public function index()
    {

    }
    public function store(EmployeeExperienceRequest $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $employeeExperience = new EmployeeExperience($request->all());

        if ($request->hasFile('experience_letter')) {
            $path = DirectoryPathHelper::experienceDirectoryPath($company_id);
            $fileName = $this->fileUpload($request->file('experience_letter'), $path);
            $employeeExperience->experience_letter = $fileName;
        }
        $employeeExperience->created_by = Auth::id();
        $employeeExperience->save();
        return response()->json(['success' => true,'message'=>'Experience entered successfully'],201);
    }
}
