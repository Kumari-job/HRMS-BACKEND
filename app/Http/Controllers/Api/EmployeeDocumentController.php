<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DirectoryPathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeDocumentRequest;
use App\Models\EmployeeDocument;
use App\Traits\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeDocumentController extends Controller
{
    use FileHelper;


    // store and update 
    public function store(EmployeeDocumentRequest $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $employee_id = $request->input('employee_id');

        $employeeDocument = EmployeeDocument::updateOrCreate(['employee_id' => $employee_id], ['employee_id' => $employee_id]);

        if ($request->hasFile('citizenship_back')) {
            $path = DirectoryPathHelper::citizenshipBackDirectoryPath($company_id, $employee_id);
            $fileName = $this->fileUpload($request->file('citizenship_back'), $path);
            $employeeDocument->citizenship_back = $fileName;
        }
        if ($request->hasFile('citizenship_front')) {
            $path = DirectoryPathHelper::citizenshipFrontDirectoryPath($company_id, $employee_id);
            $fileName = $this->fileUpload($request->file('citizenship_front'), $path);
            $employeeDocument->citizenship_front = $fileName;
        }
        if ($request->hasFile('driving_license')) {
            $path = DirectoryPathHelper::drivingLicenseDirectoryPath($company_id, $employee_id);
            $fileName = $this->fileUpload($request->file('driving_license'), $path);
            $employeeDocument->driving_license = $fileName;
        }
        if ($request->hasFile('passport')) {
            $path = DirectoryPathHelper::passportDirectoryPath($company_id, $employee_id);
            $fileName = $this->fileUpload($request->file('passport'), $path);
            $employeeDocument->passport = $fileName;
        }
        if ($request->hasFile('pan_card')) {
            $path = DirectoryPathHelper::panCardDirectoryPath($company_id, $employee_id);
            $fileName = $this->fileUpload($request->file('pan_card'), $path);
            $employeeDocument->pan_card = $fileName;
        }
        $employeeDocument->created_by = Auth::id();
        $employeeDocument->save();
        return response()->json(['success' => true, 'message' => 'Document updated successfully.'], 201);
    }
}
