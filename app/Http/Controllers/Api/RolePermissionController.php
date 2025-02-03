<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{

    public function permissionsByRole($id): JsonResponse
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $role = Role::where(['id' =>  $id, 'company_id' => $company_id])->first();
        if($role){
            $permissions = $role->permissions;
            return response()->json(['success' => true, 'data' => $permissions], 200);
        }
        return response()->json(['error' => true, 'message' => 'Role not found'], 400);

    }

    public function store(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                'permission_ids' => 'array',
                'role_id' => 'required|exists:roles,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
            }
            $role = Role::findById($request->role_id);
            $permissions = Permission::whereIn('id', $request->permission_ids)->get();
            $role->syncPermissions($permissions);
            return response()->json(['success' => true, 'message' => 'Role permissions assigned successfully.'], 201);
        
        }catch(Exception $e)
        {
            Log::error("Unable to create: ". $e->getMessage());
            return response()->json(['error'=>true, 'message'=>'Unable to store role and permission']);
        }

    }
}
