<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\SelectedCompanyController;
use App\Http\Middleware\VerifyCommonToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('users/store-idp-user-id', [AuthenticationController::class, 'storeIdpUserID'])->middleware('verify_common_token');;
Route::post('users/generate-access-token', [AuthenticationController::class, 'generateAccessToken'])->middleware('verify_common_token');;


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [AuthenticationController::class, 'logout']);

    Route::post('select-company', [SelectedCompanyController::class, 'selectCompany']);
    Route::get('selected-company', [SelectedCompanyController::class, 'selectedCompany']);
    Route::get('user-list',[SelectedCompanyController::class, 'userList']);
    Route::delete('delete-selected-company', [SelectedCompanyController::class, 'destroy']);
    Route::post('selected-company/disable',[SelectedCompanyController::class,'disableCompanyUser']);
    Route::get('selected-company/disable-status/{company_id}/{user_id}',[SelectedCompanyController::class,'checkDisableStatus']);
});