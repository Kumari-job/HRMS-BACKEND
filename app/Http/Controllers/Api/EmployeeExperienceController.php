<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DirectoryPathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeExperienceRequest;
use App\Http\Resources\EmployeeExperienceResource;
use App\Models\Employee;
use App\Models\EmployeeExperience;
use App\Traits\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeExperienceController extends Controller
{
    use FileHelper;
    public function index(string $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        $query = EmployeeExperience::query();

        $employee_experiences = $query->where('employee_id',$employee_id)->latest()->get();
        return EmployeeExperienceResource::collection($employee_experiences);
    }
    public function store(EmployeeExperienceRequest $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $employeeExperience = new EmployeeExperience($request->except('experience_letter'));

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
