<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DirectoryPathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeEducationRequest;
use App\Http\Resources\EmployeeEducationResource;
use App\Models\Employee;
use App\Models\EmployeeEducation;
use App\Traits\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeEducationController extends Controller
{
    use FileHelper;

    public function index(Request $request , string $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        $query = EmployeeEducation::query();

        $employee_educations = $query->where('employee_id',$employee_id)->latest()->get();
        return EmployeeEducationResource::collection($employee_educations);
    }
    public function store(EmployeeEducationRequest $request)
    {
        try {

            $company_id = Auth::user()->selectedCompany->company_id;
            $employeeEducation = new EmployeeEducation($request->except('certificate'));

            if ($request->hasFile('certificate')) {
                $path = DirectoryPathHelper::educationDirectoryPath($company_id);
                $fileName = $this->fileUpload($request->file('certificate'), $path);
                $employeeEducation->certificate = $fileName;
            }
            $employeeEducation->created_by = Auth::id();
            $employeeEducation->save();
            return response()->json(['success' => true, 'message' => 'Education created successfully'], 201);
        }catch (\Exception $exception){
            Log::error('Unable to create education '.$exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to create education"], 400);
        }
    }
}
