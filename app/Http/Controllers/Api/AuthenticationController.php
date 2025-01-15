<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{

    // temp 
    public function storeIdpUserID(Request $request): JsonResponse
    {
        $request->validate([
            'idp_user_id' => 'required|unique:users,idp_user_id',
            'client_app' => 'required|in:hrms,HRMS',
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'image_path' => 'nullable'
        ]);

        $user = new User;
        $user->idp_user_id = $request->idp_user_id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->image_path = $request->image_path;
        $user->save();

        return response()->json(['success' => true, 'message' => 'User added in HRMS application'], 201);
    }


    public function syncIdpUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'idp_user_id' => 'required',
            'name' => 'nullable',
            'email' => 'required|email',
            'mobile' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => 'Unprocessable content.'], 422);
        }

        User::updateOrCreate(['idp_user_id' => $request->idp_user_id], [
            'idp_user_id' => $request->idp_user_id,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ]);

        return response()->json(['success' => true, 'message' => 'User synced successfully.'], 201);
    }

    public function generateAccessToken(Request $request)
    {
        try {
            $request->validate([
                'idp_user_id' => 'required|exists:users,idp_user_id',
            ]);
            $user = User::where('idp_user_id', $request->idp_user_id)->first();
   
            $tokenResult = $user->createToken('Personal Access Token');

            $token = $tokenResult->token;

            $token->expires_at = now()->addMonth();
            $token->save();


            return response()->json([
                'success' => true,
                'data' => [
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expire_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ]
            ], 200);
        } catch (\Exception $exception) {
            Log::error('Unable to generate access token: ' . $exception->getMessage());
        }
    }


    public function logout()
    {
        auth()->user()->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => 'Successfully Logged out from CRM application'
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => true, 'message' => 'User not found.'], 400);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully.'], 200);
    }
}
