<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeContractRequest;
use App\Models\EmployeeContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeContractController extends Controller
{
    public function index()
    {

    }

    public function store(EmployeeContractRequest $request)
    {
        try {
            $employeeContract =new EmployeeContract($request->all());
            $employeeContract->created_by = Auth::id();
            $employeeContract->save();
            return response()->json([['success'=>true ,'message'=>'Employee Contract created successfully.']], 201);
        }catch (\Exception $exception){
            Log::error('Unable to store Employee Contract: '.$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to create employee contract'],400);
        }

    }
}
