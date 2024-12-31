<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
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
        $data = $request->except(['experience_letter','from_date','to_date']);
        $from_date = $request->filled('from_date_nepali') ? DateHelper::nepaliToEnglish($request->from_date_nepali) : $request->from_date;
        $to_date = $request->filled('to_date_nepali') ? DateHelper::nepaliToEnglish($request->to_date_nepali) : $request->to_date;

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $employeeExperience->fill($data);
        if ($request->hasFile('experience_letter')) {
            $path = DirectoryPathHelper::experienceDirectoryPath($company_id, $request->employee_id);
            $fileName = $this->fileUpload($request->file('experience_letter'), $path);
            $employeeExperience->experience_letter = $fileName;
        }
        $employeeExperience->created_by = Auth::id();

        $employeeExperience->save();
        return response()->json(['success' => true,'message'=>'Experience entered successfully'],201);
    }
    public function update(EmployeeExperienceRequest $request, $id)
    {
        try {
            $company_id = Auth::user()->selectedCompany->company_id;
            $employeeExperience = EmployeeExperience::find($id);
            if (!$employeeExperience) {
                return response()->json(['error' => true, 'message' => 'Experience not found'], 404);
            }
            $data = $request->except(['experience_letter','from_date','to_date']);
            $from_date = $request->filled('from_date_nepali') ? DateHelper::nepaliToEnglish($request->from_date_nepali) : $request->from_date;
            $to_date = $request->filled('to_date_nepali') ? DateHelper::nepaliToEnglish($request->to_date_nepali) : $request->to_date;

            $data['from_date'] = $from_date;
            $data['to_date'] = $to_date;

            if ($request->hasFile('experience_letter')) {
                $path = DirectoryPathHelper::experienceDirectoryPath($company_id, $employeeExperience->employee_id);
                if ($employeeExperience->experience_letter) {
                    $this->fileDelete($path, $employeeExperience->experience_letter);
                }
                $fileName = $this->fileUpload($request->file('experience_letter'), $path);
                $data['experience_letter'] = $fileName;
            }
            $employeeExperience->updated_by = Auth::id();
            $employeeExperience->update($data);
            return response()->json(['success' => true, 'message' => 'Experience updated successfully'], 200);
        }catch (\Exception $exception){
            Log::error('Unable to update experience '.$exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to update experience"], 400);
        }
    }

    public function destroy($id)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $employeeExperience = EmployeeExperience::find($id);
        if(!$employeeExperience){
            return response()->json(['error' => true,'message'=>'Experience not found'],404);
        }
        if ($employeeExperience->experience_letter) {
            $path = DirectoryPathHelper::experienceDirectoryPath($company_id, $employeeExperience->employee_id);
            $this->fileDelete($path, $employeeExperience->experience_letter);
        }
        $employeeExperience->delete();
        return response()->json(['success' => true,'message'=>'Experience deleted successfully'],200);
    }
}
