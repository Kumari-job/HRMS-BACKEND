<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\SelectedCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::with(['roles'=>function ($query) {
            $query->where('company_id',Auth::user()->selectedCompany->company_id);
        }]);

        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if (in_array($key, ['id', 'idp_user_id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }

        $users = $query->latest()->paginate($request->page_size ?? 10);
        return UserResource::collection($users);
    }

    public function profile()
    {
        $id = Auth::id();
        $user = User::find($id);

        return new UserResource($user);
    }

    public function migrateEmployeeData(Request $request)
    {
        try {
            $company_id = Auth::user()->selectedCompany->company_id;
            $validator = Validator::make($request->all(), [
                'ids' => ['required', 'array'],
            ]);
            $ids = $request->ids;
            if ($validator->fails()) {
                return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
            }
            DB::beginTransaction();
            foreach ($ids as $id) {
                $employee = Employee::select('id', 'name', 'email')->find($id);
                if (!$employee) {
                    return response()->json(['error' => true, 'message' => 'Employee not found'], 404);
                }
                $user = User::firstOrNew(['employee_id' => $employee->id]);

                $user->name = $employee->name;
                $user->email = $employee->email;
                $user->employee_id = $employee->id;

                if (!$user->exists) {
                    $user->password = Hash::make('test@123');
                    $user->is_password_changed = false;
                }

                $user->save();
                if ($user)
                {
                    $selectedCompany =  SelectedCompany::updateOrCreate(
                        [
                            'user_id' => $user->id,
                        ],
                        [
                            'company_id' => $company_id,
                            'user_id' => $user->id,
                        ]
                    );
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Employee data successfully migrated.']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error("Unable to migrate employee to user: ".$exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to migrate employee to user"], 422);
        }
    }
}
