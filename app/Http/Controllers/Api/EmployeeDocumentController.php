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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeDocumentRequest $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $employee_id = $request->input('employee_id');
        if(EmployeeDocument::where('employee_id',$employee_id)->exists()){
            return response()->json(['error'=>true,'message'=>'Employee Document Already Exist'],403);
        }
        $employeeDocument = new EmployeeDocument($request->only(['employee_id']));
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
        return response()->json(['success' => true,'message'=>'Document added successfully.'],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $employee_id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeDocumentRequest $request, string $employee_id)
    {
        try {
            $employeeDocument = EmployeeDocument::where('employee_id', $employee_id)->firstOrFail();
            $company_id = Auth::user()->selectedCompany->company_id;
            $documentsToUpdate = [
                'citizenship_back' => DirectoryPathHelper::citizenshipBackDirectoryPath($company_id, $employee_id),
                'citizenship_front' => DirectoryPathHelper::citizenshipFrontDirectoryPath($company_id, $employee_id),
                'driving_license' => DirectoryPathHelper::drivingLicenseDirectoryPath($company_id, $employee_id),
                'passport' => DirectoryPathHelper::passportDirectoryPath($company_id, $employee_id),
                'pan_card' => DirectoryPathHelper::panCardDirectoryPath($company_id, $employee_id),
            ];

            $data = [];
            foreach ($documentsToUpdate as $documentField => $path) {
                if ($request->hasFile($documentField)) {
                    if ($employeeDocument->$documentField) {
                        $this->fileDelete($path, $employeeDocument->$documentField);
                    }

                    $fileName = $this->fileUpload($request->file($documentField), $path);
                    $data[$documentField] = $fileName;
                }
            }

            if (!empty($data)) {
                $employeeDocument->update($data);
            }
            return response()->json(['success' => true, 'message' => 'Employee document updated successfully'], 200);
        }catch (\Exception $e){
            Log::error("Unable to update Employee document: " . $e);
            return response()->json(['error' => true, 'message' => 'Unable to update the document'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
