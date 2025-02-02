<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Traits\ActivityLoggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $roles = Role::where('company_id', $company_id)->latest()->get();
        return RoleResource::collection($roles);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'guard_name' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $company_id = Auth::user()->selectedCompany->company_id;
        if (Role::where('name', $request->name)
            ->where('company_id', $company_id)
            ->exists()
        ) {
            return response()->json(['error' => true, 'message' => 'Similar role already exists.'], 400);
        }
        $role = new Role($request->except('company_id'));
        $role->company_id = $company_id;
        $role->save();
        return response()->json(['success' => true, 'message' => 'Role created successfully.', 'data' => $role], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'guard_name' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $company_id = Auth::user()->selectedCompany->company_id;
        if (Role::where('name', $request->name)
            ->where('company_id', $company_id)
            ->where('id', '!=', $id)
            ->exists()
        ) {
            return response()->json(['error' => true, 'message' => 'Similar role already exists.'], 400);
        }
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['error' => true, 'message' => 'Role not found.'], 404);
        }
        $data = $request->all();
        $role->update($data);
        return response()->json(['success' => true, 'message' => 'Role updated Successfully.'], 201);
    }

    public function destroy(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        $ids = $request->ids;
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $roles = Role::whereIn('id', $ids);
        $count = $roles->count();
        if ($count > 0) {
            $roles->delete();
            return response()->json(['success' => true, 'message' => 'Role deleted successfully.'], 200);
        }
        return response()->json(['success' => true, 'message' => 'Role deleted successfully.'], 200);
    }
}
