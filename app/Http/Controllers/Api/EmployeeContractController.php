<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeBenefitRequest;
use App\Http\Requests\EmployeeContractRequest;
use App\Models\EmployeeBenefit;
use App\Models\EmployeeContract;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeContractController extends Controller
{
    public function store(EmployeeContractRequest $request, EmployeeBenefitRequest $employeeBenefitRequest)
    {
        try {
            $employeeContract =new EmployeeContract($request->all());
            $employeeContract->created_by = Auth::id();
            $employeeContract->save();
            $employeeBenefit = new EmployeeBenefit($employeeBenefitRequest->all());
            $employeeBenefit->created_by = Auth::id();
            $employeeBenefit->save();

            return response()->json(['success'=>true ,'message'=>'Employee contract created successfully.'], 201);
        }catch (Exception $exception){
            Log::error('Unable to store Employee Contract: '.$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to create employee contract'],400);
        }

    }

    public function update(EmployeeContractRequest $request, $id)
    {
        try{
            $employeeContract = EmployeeContract::find($id);
            if (!$employeeContract) {
                return response()->json(['error'=>true ,'message'=>'Employee contract not found'], 404);
            }
            $data = $request->all();
            $data['updated_by'] = Auth::id();
            $employeeContract->update($data);
            return response()->json(['success'=>true ,'message'=>'Employee contract updated successfully.'], 200);
        }catch (Exception $exception){
            Log::error('Unable to update Employee Contract: '.$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to update employee contract'],400);
        }
    }

    public function destroy($id)
    {
        try{
            $employeeContract = EmployeeContract::find($id);
            if (!$employeeContract) {
                return response()->json(['error'=>true ,'message'=>'Employee contract not found'], 404);
            }
            $employeeContract->delete();
            return response()->json(['success'=>true ,'message'=>'Employee contract deleted successfully.'], 200);
        }catch (Exception $exception){
            Log::error('Unable to delete Employee Contract: '.$exception->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to delete employee contract'], 400);
        }

    }
}
