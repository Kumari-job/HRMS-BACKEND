<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyLeaveRequest;
use App\Http\Resources\CompanyLeaveResource;
use App\Models\CompanyLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompanyLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CompanyLeave::with('createdBy','updatedBy');

        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if (in_array($key, ['id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }

        $companyLeaves = $query->latest()->paginate($request->page_size ?? 10);
        return CompanyLeaveResource::collection($companyLeaves);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyLeaveRequest $request)
    {
        try{
            $company_id = Auth::user()->selectedCompany->company_id;
            if (CompanyLeave::where('company_id', $company_id)->where('name',$request->name)->where('year',$request->year)->exists()) {
                return response()->json(['error'=>true,'message'=>'Leave already exist for this year.'],400);
            }
            $data = $request->validated();
            $companyLeave = new CompanyLeave();
            $companyLeave->fill($data);
            $companyLeave->company_id = Auth::id();
            $companyLeave->created_by = Auth::id();
            $companyLeave->save();
            return response()->json(['success' => true, 'message' => 'Company leave added successfully.'],201);
        }catch (\Exception $exception){
            return response()->json(['success' => false, 'message' => $exception->getMessage()],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $companyLeave = CompanyLeave::with('createdBy','updatedBy')->find($id);
        if (!$companyLeave) {
            return response()->json(['error'=>true,'message'=>'Leave not found'],404);
        }
        return new CompanyLeaveResource($companyLeave);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyLeaveRequest $request, string $id)
    {
        try{
            $company_id = Auth::user()->selectedCompany->company_id;

            $companyLeave = CompanyLeave::with('createdBy','updatedBy')->find($id);

            if (!$companyLeave) {
                return response()->json(['error'=>true,'message'=>'Leave not found'],404);
            }
            if (CompanyLeave::where('company_id', $company_id)->where('name',$request->name)->where('year',$request->year)
                ->where('id','!=',$request->id)->exists()) {
                return response()->json(['error'=>true,'message'=>'Leave already exist for this year.'],400);
            }
            $data = $request->validated();
            $companyLeave->fill($data);
            $companyLeave->updated_by = Auth::id();
            $companyLeave->update();
            return response()->json(['success' => true, 'message' => 'Leave updated successfully.'],200);
        }catch (\Exception $exception){
            Log::error("Unable to update company leave: ". $exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to update leave"],500);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'ids' => 'array'
            ]);
            $ids = $request->ids;
            if ($validator->fails()) {
                return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
            }
            $company_leaves = CompanyLeave::whereIn('id', $ids);
            $count = $company_leaves->count();
            if ($count > 0) {
                $deleteStatus = $company_leaves->delete();

                return response()->json(['success' => true, 'message' => 'Leaves deleted successfully.'], 200);
            }
            return response()->json(['error' => true, 'message' => 'Leaves not found.'], 404);
        } catch (\Exception $exception) {
            Log::error("Unable to delete company leave: ". $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to delete leaves'], 500);
        }
    }
}
