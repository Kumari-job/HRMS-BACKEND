<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeFamilyRequest;
use App\Models\EmployeeFamily;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeFamilyController extends Controller
{
    public function store(EmployeeFamilyRequest $request)
    {
        try {
            $employeeFamily = new EmployeeFamily($request->all());
            $employeeFamily->created_by = Auth::id();
            $employeeFamily->save();
            return response()->json(['success' => true,'message'=>'Employee family entered successfully'],201);
        } catch (\Exception $exception)
        {
            Log::error("Unable to store Employee family : {$exception->getMessage()}");
            return response()->json(['error' => true,'message'=>'Unable to store employee family'],400);
        }
    }
}
