<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeOnboardingRequest;
use App\Models\EmployeeOnboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeOnboardingController extends Controller
{
    public function index()
    {

    }

    public function store(EmployeeOnboardingRequest $request)
    {
        try {
            $employeeOnboarding = new EmployeeOnboarding($request->all());
            $employeeOnboarding->created_by = Auth::id();
            $employeeOnboarding->save();
            return response()->json(['success' => true, 'message' => 'Employee Onboarding created successfully.'], 201);
        }catch (\Exception $exception){
            Log::error("Unable to create Employee Onboarding: {$exception->getMessage()}");
            return response()->json(['error' => true, 'message' => 'Unable to create employee onboarding'], 400);
        }
    }
}
