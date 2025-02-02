<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\MessageHelper;
use App\Models\User;
use App\Traits\ActivityLoggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index($user_id): JsonResponse
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $user = User::where('id', $user_id)->first();
        if ($user) {
            $role = $user->roles()->first();
            if (!$role) {
                return response()->json(['error' => true, 'message' => 'No roles assigned to the selected user.'], 400);
            }
        } else {
            return response()->json(['error' => true, 'message' => 'User does not exists.'], 400);
        }
        return response()->json(['success' => true, 'data' => $role], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }

        $user = User::where('id', $request->user_id)->first();
        $company_id = Auth::user()->selectedCompany->company_id;
        $role = $user->roles()->where('company_id', $company_id)->first();
        if ($role) {
            $user->removeRole($role);
        }
        $new_role = Role::findById($request->role_id);
        $user->assignRole($new_role);
        return response()->json(['success' => true, 'message' => 'Role assigned to user successfully.'], 201);
    }

    public function destroy($user_id): JsonResponse
    {
        $user = User::where('id', $user_id)->first();
        $company_id = Auth::user()->selectedCompany->company_id;
        if ($user) {
            $role = $user->roles()->where('company_id', $company_id)->first();
            if ($role) {
                $user->removeRole($role);
                return response()->json(['success' => true, 'message' => 'User role deleted successfully.'], 200);
            } else {
                return response()->json(['error' => true, 'message' => 'Role does not exists.'], 400);
            }
        } else {
            return response()->json(['error' => true, 'message' => 'User does not exists.'], 400);
        }
    }
}
