<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeBenefitRequest;
use App\Models\EmployeeBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeBenefitController extends Controller
{

    public function store(EmployeeBenefitRequest $request)
    {
        try {
            $employeeBenefit = new EmployeeBenefit($request->all());
            $employeeBenefit->created_by = Auth::id();
            $employeeBenefit->save();
            return response()->json(['success' => true, 'message' => 'Employee Benefit added successfully.'],201);
        }catch (\Exception $exception){
            Log::error('Unable to create Employee Benefit: '.$exception->getMessage());
            return response()->json(['success' => false, 'message' => "Unable to create employee benefit"],400);
        }
    }
}
