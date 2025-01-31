<?php

namespace App\Http\Controllers;

use App\Helpers\MessageHelper;
use App\Models\User;
use App\Traits\ActivityLoggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->get();
        return response()->json(['success' => true, 'data' => $permissions], 201);
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
        if (Permission::where('name', $request->name)
            ->exists()
        ) {
            return response()->json(['error' => true, 'message' => 'Similar permission already exists.'], 400);
        }
        $permission = new Permission($request->all());
        $permission->save();
        return response()->json(['success' => true, 'message' => 'Permission created successfully.'], 201);
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
        if (Permission::where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists()
        ) {
            return response()->json(['error' => true, 'message' => 'Similar permission already exists.'], 400);
        }
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['error' => true, 'message' => 'Permission not found.'], 404);
        }
        $data = $request->all();
        $permission->update($data);
        return response()->json(['success' => true, 'message' => 'Permission updated Successfully.'], 201);
    }

    public function destroy(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        $ids = $request->ids;
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $permissions = Permission::whereIn('id', $ids);
        $count = $permissions->count();

        if ($count > 0) {
            $permissions->delete();
            return response()->json(['success' => true, 'message' => 'Permission deleted successfully.'], 201);
        }

        return response()->json(['error' => true, 'message' => 'Permissions not found.'], 400);
    }

    public function permissionsByUser($user_id): JsonResponse {
        $user = User::where('id', $user_id)->first();
        $permissions = $user->getAllPermissions();
        return response()->json(['success' => true, 'data' => $permissions], 200);
    }

}
