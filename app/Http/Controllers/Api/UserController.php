<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();

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
}
