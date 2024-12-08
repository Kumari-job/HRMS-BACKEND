<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthenticationController extends Controller
{
    public function storeIdpUserID(Request $request): JsonResponse
    {
        $request->validate([
            'idp_user_id' => 'required|unique:users,idp_user_id',
            'client_app' => 'required|in:hrms,HRMS'
        ]);

        $user = new User;
        $user->idp_user_id = $request->idp_user_id;
        $user->save();

        return response()->json(['success' => true, 'message' => 'User added in TMS application'], 201);
    }

    public function generateAccessToken(Request $request)
    {
        try {
            $request->validate([
                'idp_user_id' => 'required|exists:users,idp_user_id',
                'client_app' => 'required|in:hrms,HRMS'
            ]);

            $user = User::where('idp_user_id', $request->idp_user_id)->first();

            // access token -> laravel passport

            $tokenResult = $user->createToken('Personal Access Token');

            $token = $tokenResult->token;

            $token->expires_at = now()->addMonth(1);
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
        }catch (\Exception $exception){
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
