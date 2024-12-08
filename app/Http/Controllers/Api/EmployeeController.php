<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DirectoryPathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Traits\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    use FileHelper;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    public function store(EmployeeRequest $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;

        if (Employee::where('company_id', $company_id)->where('name',$request['name'])->where('email',$request['email'])->exists()) {
            return response()->json(['error'=>true,"message"=>"Employee already exists"],400);
        }

        $employee = new Employee($request->except('image','citizenship_front_image','citizenship_back_image'));

        if ($request->hasFile('image')) {
            $path = DirectoryPathHelper::employeeImageDirectoryPath($company_id);
            $fileName = $this->fileUpload($request->file('image'), $path);
            $employee->image = $fileName;
        }
        if ($request->hasFile('citizenship_front_image')) {
            $path = DirectoryPathHelper::citizenshipFrontDirectoryPath($company_id);
            $fileName = $this->fileUpload($request->file('citizenship_front_image'), $path);
            $employee->citizenship_front_image = $fileName;
        }
        if ($request->hasFile('citizenship_back_image')) {
            $path = DirectoryPathHelper::citizenshipBackDirectoryPath($company_id);
            $fileName = $this->fileUpload($request->file('citizenship_back_image'), $path);
            $employee->citizenship_back_image = $fileName;
        }
        $employee->company_id = $company_id;
        $employee->save();
        return response()->json(['success'=>true,"message"=>"Employee added successfully"],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
