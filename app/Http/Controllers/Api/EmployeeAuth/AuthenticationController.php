<?php

namespace App\Http\Controllers\Api\EmployeeAuth;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeLoginRequest;
use App\Models\User;
use App\Notifications\OTPNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function login(EmployeeLoginRequest $request)
    {
            $data = $request->validated();
            $user = User::where('email', $data['email'])->whereHas('employee',function ($query) use ($data){
                $query->where('company_id', $data['company_id']);
            })->first();

        if ($user?->password && Hash::check($request->password, $user->password)) {
            $user->last_login_at = Carbon::now();
            $user->save();
            $token = $user->createToken('Employee Access Token', ['employee'])->accessToken;
            return response()->json(['success' => true, 'message' => 'Login Successful.', 'token' => $token], 200);
        } else {
            return response()->json(['error' => true, 'message' => 'Invalid Credentials'], 401);
        }
    }

    public function logout()
    {
        auth()->user()->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:4|confirmed|different:current_password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => true, 'message' => 'Current password is incorrect'], 400);
        }
        $user->is_password_changed = 1;
        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json(['success' => true, 'message' => 'Password successfully changed'], 200);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'company_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $user = User::where('email', $request->email)->whereHas('employee',function ($query) use ($request){
            $query->where('company_id',$request->company_id);
        })->first();

        if (!$user) {
            return response()->json(['error' => 'true', 'message' => 'No employee found with this email or company.'], 400);
        }
        do {
            $otp = rand(10000, 99999);
            $existingOtp = User::where('otp', $otp)->first();
        } while ($existingOtp);

        $token = Password::createToken($user);

        $user->notify(new OTPNotification($otp, $token, $request->email));
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(60);
        $user->token = $token;
        $user->save();
        return response()->json(['success' => true, 'message' => 'OTP and reset link sent to email.'], 200);
    }

    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        $user = User::where('token', $request->token)->first();
        if (!$user) {
            return response()->json(['error' => 'true', 'message' => 'Unidentified token.'], 400);
        }

        if (!Password::tokenExists($user, $request->token)) {
            return response()->json(['error' => true, 'message' => 'Invalid or expired reset token'], 400);
        }
        $token = Password::createToken($user);
        $user->token = $token;
        $user->save();
        return response()->json(['success' => true, 'message' => 'The token is valid.', 'data' => ['isValid' => true, 'token' => $token]], 200);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|integer',
        ]);

        $user = User::where('otp', $request->otp)->first();
        if (!$user) {
            return response()->json(['error' => 'true', 'message' => 'Invalid OTP'], 400);
        }

        if ($user->otp_expires_at < now()) {
            return response()->json(['error' => true, 'message' => 'OTP has expired'], 400);
        }

        return response()->json(['success' => true, 'message' => 'OTP verified.', 'data' => ['token' => $user->token]], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|min:4|confirmed',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $user = User::where('token', $request->token)->first();

        if (!$user) {
            return response()->json(['error' => true, 'message' => 'Invalid or expired reset token'], 400);
        }


        if (!Password::tokenExists($user, $request->token)) {
            return response()->json(['error' => true, 'message' => 'Invalid or expired reset token'], 400);
        }

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['error' => true, 'message' => 'New password cannot be same as old password.'], 400);
        }
        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->token = null;
        $user->save();
        Password::deleteToken($user);
        return response()->json(['success' => true, 'message' => 'Password has been reset.'], 200);
    }
}
