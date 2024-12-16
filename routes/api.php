<?php

use App\Http\Controllers\Api\AssetCategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AssetDisposeController;
use App\Http\Controllers\Api\AssetSaleController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeAddressController;
use App\Http\Controllers\Api\EmployeeBankController;
use App\Http\Controllers\Api\EmployeeBenefitController;
use App\Http\Controllers\Api\EmployeeContractController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeEducationController;
use App\Http\Controllers\Api\EmployeeExperienceController;
use App\Http\Controllers\Api\EmployeeFamilyController;
use App\Http\Controllers\Api\EmployeeOnboardingController;
use App\Http\Controllers\Api\SelectedCompanyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Middleware\VerifyCommonToken;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('users/store-idp-user-id', [AuthenticationController::class, 'storeIdpUserID'])->middleware('verify_common_token');;
Route::post('users/generate-access-token', [AuthenticationController::class, 'generateAccessToken'])->middleware('verify_common_token');;


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [AuthenticationController::class, 'logout']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('update-preferred-calendar',[UserController::class, 'updatePreferredCalendar']);

    Route::post('select-company', [SelectedCompanyController::class, 'selectCompany']);
    Route::get('selected-company', [SelectedCompanyController::class, 'selectedCompany']);
    Route::get('user-list',[SelectedCompanyController::class, 'userList']);
    Route::delete('delete-selected-company', [SelectedCompanyController::class, 'destroy']);
    Route::post('selected-company/disable',[SelectedCompanyController::class,'disableCompanyUser']);
    Route::get('selected-company/disable-status/{company_id}/{user_id}',[SelectedCompanyController::class,'checkDisableStatus']);

    Route::prefix('data')->group(function () {
        Route::get('gender',[DataController::class, 'gender']);
        Route::get('marital-status',[DataController::class, 'maritalStatus']);
        Route::get('blood-group',[DataController::class, 'bloodGroup']);
        Route::get('religion',[DataController::class, 'religion']);
        Route::get('employment-type',[DataController::class, 'employmentType']);
        Route::get('contract-type',[DataController::class, 'contractType']);
    });

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

    Route::prefix('department')->group(function () {
        Route::get('list',[DepartmentController::class, 'index']);
        Route::post('store',[DepartmentController::class, 'store']);
        Route::get('show/{id}',[DepartmentController::class, 'show']);
        Route::post('update/{id}',[DepartmentController::class, 'update']);
        Route::post('destroy',[DepartmentController::class, 'destroy']);
        Route::get('trashed',[DepartmentController::class, 'trashed']);
        Route::post('restore',[DepartmentController::class, 'restore']);
        Route::post('force-delete',[DepartmentController::class, 'forceDelete']);
    });

    Route::prefix('employee')->group(function () {
        Route::get('list',[EmployeeController::class, 'index']);
        Route::post('store',[EmployeeController::class, 'store']);
        Route::get('show/{id}',[EmployeeController::class, 'show']);
        Route::post('update/{id}',[EmployeeController::class, 'update']);
        Route::post('update-image/{id}',[EmployeeController::class, 'updateImage']);
        Route::post('remove-image/{id}',[EmployeeController::class, 'removeImage']);
        Route::post('destroy',[EmployeeController::class, 'destroy']);
        Route::get('trashed',[EmployeeController::class, 'trashed']);
        Route::post('restore',[EmployeeController::class, 'restore']);
        Route::post('force-delete',[EmployeeController::class, 'forceDelete']);

        Route::prefix('onboard')->group(function () {
            Route::post('store',[EmployeeOnboardingController::class, 'store']);
            Route::post('update/{id}',[EmployeeOnboardingController::class, 'update']);
            Route::post('destroy/{id}',[EmployeeOnboardingController::class, 'destroy']);
        });

        Route::prefix('contract')->group(function () {
            Route::post('store',[EmployeeContractController::class, 'store']);
            Route::post('update/{id}',[EmployeeContractController::class, 'update']);
            Route::post('destroy/{id}',[EmployeeContractController::class, 'destroy']);
        });

        Route::prefix('benefit')->group(function () {
            Route::post('store',[EmployeeBenefitController::class, 'store']);
            Route::post('update/{id}',[EmployeeBenefitController::class, 'update']);
        });

        Route::prefix('address')->group(function () {
            Route::post('store',[EmployeeAddressController::class, 'store']);
            Route::post('update/{id}',[EmployeeAddressController::class, 'update']);
        });

        Route::prefix('experience')->group(function () {
            Route::post('store',[EmployeeExperienceController::class, 'store']);
            Route::get('list/{employee_id}',[EmployeeExperienceController::class,'index']);
            Route::post('update/{id}',[EmployeeExperienceController::class, 'update']);
            Route::post('destroy/{id}',[EmployeeExperienceController::class,'destroy']);
        });

        Route::prefix('education')->group(function () {
            Route::post('store',[EmployeeEducationController::class, 'store']);
            Route::get('list/{employee_id}',[EmployeeEducationController::class,'index']);
            Route::post('update/{id}',[EmployeeEducationController::class,'update']);
            Route::post('destroy/{id}',[EmployeeEducationController::class,'destroy']);

        });

        Route::prefix('family')->group(function () {
            Route::post('store',[EmployeeFamilyController::class, 'store']);
            Route::get('list/{employee_id}',[EmployeeFamilyController::class,'index']);
            Route::post('update/{id}',[EmployeeFamilyController::class,'update']);
            Route::post('destroy/{id}',[EmployeeFamilyController::class,'destroy']);
        });
        Route::prefix('document')->group(function () {
            Route::post('store',[EmployeeDocumentController::class, 'store']);
            Route::post('update/{employee_id}',[EmployeeDocumentController::class, 'update']);
        });

        Route::prefix('bank')->group(function () {
            Route::post('store',[EmployeeBankController::class, 'store']);
            Route::post('update/{id}',[EmployeeBankController::class, 'update']);
            Route::post('destroy/{id}',[EmployeeBankController::class,'destroy']);
        });
    });

    Route::prefix('asset')->group(function () {
        Route::get('list',[AssetController::class, 'index']);
        Route::post('store',[AssetController::class,'store']);
        Route::get('show/{id}',[AssetController::class,'show']);
        Route::post('update/{id}',[AssetController::class,'update']);
        Route::post('destroy',[AssetController::class,'destroy']);

       Route::prefix('category')->group(function () {
           Route::get('list',[AssetCategoryController::class, 'index']);
           Route::post('store',[AssetCategoryController::class, 'store']);
           Route::post('update/{id}',[AssetCategoryController::class, 'update']);
           Route::get('show/{id}',[AssetCategoryController::class, 'show']);
           Route::post('destroy/{id}',[AssetCategoryController::class,'destroy']);
       });

       Route::prefix('vendor')->group(function () {
          Route::get('list',[VendorController::class, 'index']);
          Route::post('store',[VendorController::class, 'store']);
          Route::get('show/{id}',[VendorController::class, 'show']);
          Route::post('update/{id}',[VendorController::class, 'update']);
          Route::post('destroy/{id}',[VendorController::class,'destroy']);
       });

       Route::prefix('dispose')->group(function () {
           Route::get('list',[AssetDisposeController::class, 'index']);
           Route::post('store',[AssetDisposeController::class, 'store']);
           Route::get('show/{id}',[AssetDisposeController::class, 'show']);
           Route::post('update/{id}',[AssetDisposeController::class, 'update']);
           Route::post('destroy/{id}',[AssetDisposeController::class,'destroy']);
       });

       Route::prefix('sale')->group(function () {
           Route::get('list',[AssetSaleController::class, 'index']);
           Route::post('store',[AssetSaleController::class, 'store']);
           Route::get('show/{id}',[AssetSaleController::class, 'show']);
           Route::post('update/{id}',[AssetSaleController::class, 'update']);
           Route::post('destroy/{id}',[AssetSaleController::class,'destroy']);
       });
    });
});