<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\EmployeeController;
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

    Route::prefix('branch')->group(function () {
        Route::get('list',[BranchController::class, 'index']);
        Route::post('store',[BranchController::class, 'store']);
        Route::get('show/{id}',[BranchController::class, 'show']);
        Route::post('update/{id}',[BranchController::class, 'update']);
        Route::post('destroy',[BranchController::class, 'destroy']);
        Route::get('trashed',[BranchController::class, 'trashed']);
        Route::post('restore',[BranchController::class, 'restore']);
        Route::post('force-delete',[BranchController::class, 'forceDelete']);
    });

    Route::prefix('employee')->group(function () {
        Route::post('store',[EmployeeController::class, 'store']);
    });
});