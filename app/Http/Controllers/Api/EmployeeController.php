<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\DirectoryPathHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Jobs\ProcessEmployeeExport;
use App\Models\Employee;
use App\Traits\FileHelper;
use App\Imports\EmployeeImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    use FileHelper;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;

        $query = Employee::with(['departments','employeeContracts'=>function ($query) {
            $query->select('id','employee_id','contract_type','job_description','probation_end_at','gross_salary')
                ->latest()->first();
        },'employeeOnboardings' => function ($query) {
            $query->select('id','employee_id','employment_type','joined_at')
                ->latest()->first();
        }, 'employeeBanks' => function ($query) {
            $query->select('id', 'employee_id', 'account_name', 'account_number', 'bank_name','bank_branch')
                ->where('is_primary',1)
                ->first();
        },'employeeAddress:id,employee_id,t_district,t_street', 'employeeBenefit:id,employee_id,pan,ssf,cit,pf']);
        if (!empty($request->except('page', 'page_size', 'export'))) {
            foreach ($request->except('page', 'page_size', 'export') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if ($key === 'department_id') {
                        // departments is belongsTo relation 
                        $query->whereHas('departments', fn($q) =>
                            //departments table
                            $q->where('departments.id', $value));
                    } else if ($key === 'branch_id') {
                        // departments is belongsTo relation 
                        $query->whereHas('departments', fn($q) =>
                            // departments table
                            $q->where('departments.branch_id', $value));
                    } else if (in_array($key, ['id', 'company_id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }
        if ($request->has('export')) {
            $employees = $query->get();
            ProcessEmployeeExport::dispatch($employees, $company_id, Auth::user());
            return response()->json(['success' => true, 'message' => 'Download file is being ready. The file will be sent to your mail']);
        }

        $employees = $query->where('company_id', $company_id)->latest()->paginate($request->page_size ?? 10);
        return EmployeeResource::collection($employees);
    }


    public function store(EmployeeRequest $request)
    {
        try {
            $company_id = Auth::user()->selectedCompany->company_id;
            if (Employee::where('company_id', $company_id)->where('email', $request['email'])->exists()) {
                return response()->json(['error' => true, "message" => "Employee already exists"], 400);
            }
            $data = $request->except('date_of_birth', 'image');
            $date_of_birth = $request->filled('date_of_birth_nepali') ? DateHelper::nepaliToEnglish($request->date_of_birth_nepali) : $request->date_of_birth;

            $data['date_of_birth'] = $date_of_birth;
            $employee = new Employee();

            $employee->fill($data);
            $employee->company_id = $company_id;

            if ($request->hasFile('image')) {
                $path = DirectoryPathHelper::employeeImageDirectoryPath($company_id, $employee->id);
                $fileName = $this->fileUpload($request->file('image'), $path);
                $employee->image = $fileName;
            }
            $employee->save();
            return response()->json(['success' => true, "message" => "Employee added successfully", 'id' => $employee->id], 201);
        }catch (\Exception $exception){
            Log::error("Unable to create employee: " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['employeeAddress', 'employeeBenefit', 'employeeContracts', 'employeeDocument', 'employeeEducations', 'employeeExperiences', 'employeeFamilies', 'employeeOnboardings', 'employeeBanks', 'departments', 'assetUsages.asset:id,title,image,code,description,brand,model,serial_number,status'])->find($id);
        if (!$employee) {
            return response()->json(['error' => true, "message" => "Employee not found"], 404);
        }
        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, string $id)
    {
        try {
            $employee = Employee::find($id);
            if (!$employee) {
                return response()->json(['error' => true, "message" => "Employee not found"], 404);
            }
            if (Employee::where('name', $request['name'])->where('email', $request['email'])->where('id', '!=', $id)->exists()) {
                return response()->json(['error' => true, "message" => "Employee already exists"], 400);
            }
            $data = $request->except('image', 'date_of_birth');
            $date_of_birth = $request->filled('date_of_birth_nepali') ? DateHelper::nepaliToEnglish($request->date_of_birth_nepali) : $request->date_of_birth;

            $data['date_of_birth'] = $date_of_birth;
            if ($request->hasFile('image')) {
                $path = DirectoryPathHelper::employeeImageDirectoryPath($employee->company_id, $employee->id);
                if ($employee->image) {
                    $this->fileDelete($path, $employee->image);
                }
                $fileName = $this->fileUpload($request->file('image'), $path);
                $data['image'] = $fileName;
            }

            $employee->update($data);
            return response()->json(['success' => true, "message" => "Employee updated successfully", 'id' => $employee->id], 200);
        }catch (\Exception $exception){
            Log::error("Unable to update employee: " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to update employee"], 500);
        }
    }

    public function updateImage(Request $request, string $id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['error' => true, "message" => "Employee not found"], 404);
        }

        if ($request->hasFile('image')) {
            $path = DirectoryPathHelper::employeeImageDirectoryPath($employee->company_id, $employee->id);
            if ($employee->image) {
                $this->fileDelete($path, $employee->image);
            }
            $fileName = $this->fileUpload($request->file('image'), $path);
        }

        $employee->update(['image' => $fileName]);
        return response()->json(['success' => true, "message" => "Image updated successfully", 'id' => $employee->id], 200);
    }

    public function removeImage(Request $request, string $id)
    {
        $employee = Employee::find($id);
        $path = DirectoryPathHelper::employeeImageDirectoryPath($employee->company_id, $employee->id);
        if ($employee->image) {
            $this->fileDelete($path, $employee->image);
        }
        $employee->update(['image' => null]);
        return response()->json(['success' => true, "message" => "Image removed successfully", 'id' => $employee->id], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        $ids = $request->ids;
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $employees = Employee::whereIn('id', $ids);
        $count = $employees->count();
        if ($count > 0) {
            $deleteStatus = $employees->delete();

            return response()->json(['success' => true, 'message' => 'Employees trashed successfully.'], 200);
        }
        return response()->json(['error' => true, 'message' => 'Employees not found.'], 400);
    }

    public function trashed(Request $request)
    {
        $query = Employee::onlyTrashed();
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
        $company_id = Auth::user()->selectedCompany->company_id;
        $employees = $query->where('company_id', $company_id)->latest()->paginate($request->page_size ?? 10);
        return EmployeeResource::collection($employees);
    }
    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $ids = $request->ids;
        Employee::withTrashed()->whereIn('id', $ids)->restore();
        return response()->json(['success' => true, 'message' => 'Employee restored successfully.'], 200);
    }

    public function forceDelete(Request $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        $ids = $request->ids;
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $employees = Employee::withTrashed()->whereIn('id', $ids);
        $count = $employees->count();
        if ($count > 0) {
            foreach ($employees as $employee) {
                if ($employee->image) {
                    $path = DirectoryPathHelper::employeeImageDirectoryPath($company_id, $employee->id);
                    $this->fileDelete($path, $employee->image);
                }
            }
            $employees->forceDelete();
            return response()->json(['success' => true, 'message' => 'Employees deleted successfully.'], 200);
        }
        return response()->json(['error' => true, 'message' => 'Employees not found.'], 404);
    }

    public function employeeImport(Request $request)
    {
        try {
            $company_id = Auth::user()->selectedCompany->company_id;
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:xls,xlsx,csv'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
            }

            $path = DirectoryPathHelper::employeeImportDirectoryPath($company_id);

            $fileName = $this->fileUpload($request->file('file'), $path);

            $employeeImport = new EmployeeImport();
            $fullFilePath = storage_path('app/public/' . $path . '/' . $fileName);

            $employeeImport->import($fullFilePath);
            return response()->json(['success' => true, 'message' => 'Employee imported successfully.'], 200);
        } catch (\Exception $exception) {
            Log::error("Unable to import employee import: " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to import employee"], 422);
        }
    }

    public function downloadSample()
    {
        $filePath = 'samples/EmployeeSample.xlsx';

        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return Storage::disk('public')->download($filePath, 'EmployeeSample.xlsx');
    }
}
