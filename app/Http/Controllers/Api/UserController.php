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
    public function profile()
    {
        $id = Auth::id();
        $user = User::find($id);

        return new UserResource($user);
    }

    public function updatePreferredCalendar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'preferred_calendar' => 'required|in:english,nepali',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $user = Auth::user();
        $user->preferred_calendar = $request->input('preferred_calendar');
        $user->update();
        return response()->json(['success' => true, 'message' => 'Profile updated successfully!'],200);
    }
}
