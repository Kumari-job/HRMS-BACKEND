<?php

namespace App\Helpers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AuthHelper implements ShouldQueue
{
    public static function getRoleName(): string|null
    {
        if (Auth::guard('api')->check()) {
            $company_id = Auth::user()?->selectedCompany?->company_id;
            if ($company_id) {
                $user = Auth::user();
                $role = $user->roles()->where('company_id', $company_id)->first();
                return $role?->name;
            }
        }
        return null;
    }

    public static function getCompanyInformation($idp_user_id)
    {
        if (Auth::check()) {
            $company_id = Auth::user()->selectedCompany->company_id;
            $idpAppUrl = config('custom.client_app.idp_url');
            $commonToken = config('custom.common_token');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $commonToken,
                'Accept' => 'application/json',
            ])->get($idpAppUrl . '/api/company/get-by-id/' . $company_id . '/' . $idp_user_id);
            $companyData = $response->json();
            $company = $companyData['data'];
            return $company;
        }
        return null;
    }
    public static function getStudentCompanyInformation($company_id)
    {
        $idpAppUrl = config('custom.client_app.idp_url');
        $commonToken = config('custom.common_token');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $commonToken,
            'Accept' => 'application/json',
        ])->get($idpAppUrl . '/api/company/get-by-id/' . $company_id);
        $companyData = $response->json();
        $company = $companyData['data'];
        return $company;
    }

    public static function getIdpUser($idp_user_id)
    {
        $idpAppUrl = config('custom.client_app.idp_url');
        $responseUser = Http::withHeaders([
            'Accept' => 'application/json',
        ])->get($idpAppUrl . '/api/user-by-id/' . $idp_user_id);

        $userData = $responseUser->json();

        $user = $userData['data'];
        return $user;
    }

    public static function listUsers($searchedName = null)
    {
        $idpAppUrl = config('custom.client_app.idp_url');
        $user = Auth::user();

        $commonToken = config('custom.common_token');
        $url = $idpAppUrl . '/api/users-by-company/' . $user->selectedCompany->company_id;

        $queryParams = [];
        if (!is_null($searchedName)) {
            $queryParams['name'] = $searchedName;
        }

        $responseUser = Http::withHeaders([
            'Authorization' => 'Bearer ' . $commonToken,
            'Accept' => 'application/json',
        ])->get($url, $queryParams);

        $userData = $responseUser->json();
        $user = $userData['data'];
        return $user;
    }
}
