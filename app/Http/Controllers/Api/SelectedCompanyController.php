<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Models\SelectedCompany;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SelectedCompanyController extends Controller
{
    public function selectCompany(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $user_id = Auth::id();
        $selectCompany = SelectedCompany::where('user_id', $user_id)->first();
        if ($selectCompany) {
            $selectCompany->company_id = $request->company_id;
            $selectCompany->update();
            return response()->json(['success' => true, 'message' => 'Company changed successfully!'], 200);
        }

        $selectedCompany = new SelectedCompany();
        $selectedCompany->user_id = $user_id;
        $selectedCompany->company_id = $request->company_id;
        $selectedCompany->save();
        return response()->json(['success' => true, 'message' => 'Company selected successfully.'], 200);
    }

    public function selectedCompany() {
        if(Auth::user()->selectedCompany){
            $selectCompany = Auth::user()->selectedCompany->company_id;
            return response()->json(['success' => true, 'data' => $selectCompany], 200);
        }
        else {
            return response()->json(['error' => true, 'message' => "Selected company not found."], 400);
        }
    }

    public function destroy()
    {
        $selectedCompany = SelectedCompany::where('user_id', Auth::id())->first();
        if (!$selectedCompany) {
            return response()->json(['error' => true, 'message' => 'Company not selected yet.'], 400);
        }
        $selectedCompany->delete();
        return response()->json(['success' => true, 'message' => 'Selected Company deleted successfully.'], 200);
    }
    public function disableCompanyUser(Request $request)
    {
        $company_id = $request['company_id'];
        $user_id = $request['user_id'];

        $company = SelectedCompany::where('company_id',$company_id)->first();
        $user = User::find($user_id);

        if (!$user || !$company) {
            return response()->json(['error' => 'User or Company not found'], 404);
        }

        $idpAppUrl = config('custom.client_app.idp_url');
        $commonToken = config('custom.common_token');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $commonToken,
            'Accept' => 'application/json'
        ])->post($idpAppUrl . '/api/disable/'.$company_id.'/'.$user->idp_user_id);

        return $response->json();

    }

    public function checkDisableStatus($company_id, $user_id)
    {

        $company = SelectedCompany::where('company_id',$company_id)->first();
        $user = User::find($user_id);

        if (!$user || !$company) {
            return response()->json(['error' => 'User or Company not found'], 404);
        }

        $idpAppUrl = config('custom.client_app.idp_url');
        $commonToken = config('custom.common_token');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $commonToken,
            'Accept' => 'application/json'
        ])->get($idpAppUrl . '/api/disable-status/'.$company_id.'/'.$user->idp_user_id);

        return $response->json();

    }

    public function userList()
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $users = User::select('id','idp_user_id')->whereHas('selectedCompany', function($query) use($company_id){
            $query->where('company_id',$company_id);
        })->get();
        return response()->json(['success'=>true,'data'=>$users],200);
    }
}
