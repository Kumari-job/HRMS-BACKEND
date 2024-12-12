<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeAddressRequest;
use App\Models\EmployeeAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeAddressController extends Controller
{
    public function store(EmployeeAddressRequest $request)
    {
        try{
            $employeeAddress = new EmployeeAddress($request->all());
            $employeeAddress->created_by = Auth::id();
            $employeeAddress->save();
            return response()->json(['success' => true, 'message' => 'Address added successfully.'],201);
        }catch (\Exception $exception){
            Log::error('Unable to create address: '.$exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to create address'],500);
        }


    }
}
