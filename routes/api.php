<?php

use App\Http\Controllers\Api\AssetCategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AssetDisposeController;
use App\Http\Controllers\Api\AssetMaintenanceController;
use App\Http\Controllers\Api\AssetSaleController;
use App\Http\Controllers\Api\AssetUsageController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CompanyHolidayController;
use App\Http\Controllers\Api\CompanyLeaveController;
use App\Http\Controllers\Api\CompanyProfileController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DepartmentEmployeeController;
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
use App\Http\Controllers\Api\PayrollSettingController;
use App\Http\Controllers\Api\SelectedCompanyController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Middleware\VerifyCommonToken;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// temp 
Route::post('users/store-idp-user-id', [AuthenticationController::class, 'storeIdpUserID'])->middleware('verify_common_token');
Route::post('users/generate-access-token', [AuthenticationController::class, 'generateAccessToken'])->middleware('verify_common_token');

// client sync and access token 
Route::group(['middleware' => 'client'], function () {
    Route::post('sync-user', [AuthenticationController::class, 'syncIdpUser']);
    Route::post('access-token', [AuthenticationController::class, 'generateAccessToken']);
});


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [AuthenticationController::class, 'logout']);

    Route::prefix('user')->group(function () {
        Route::get('list', [UserController::class, 'index']);
        Route::get('profile', [UserController::class, 'profile']);
    });


    Route::post('select-company', [SelectedCompanyController::class, 'selectCompany']);
    Route::get('selected-company', [SelectedCompanyController::class, 'selectedCompany']);
    Route::get('user-list', [SelectedCompanyController::class, 'userList']);
    Route::delete('delete-selected-company', [SelectedCompanyController::class, 'destroy']);
    Route::post('selected-company/disable', [SelectedCompanyController::class, 'disableCompanyUser']);
    Route::get('selected-company/disable-status/{company_id}/{user_id}', [SelectedCompanyController::class, 'checkDisableStatus']);

    Route::prefix('data')->group(function () {
        Route::get('gender', [DataController::class, 'gender']);
        Route::get('marital-status', [DataController::class, 'maritalStatus']);
        Route::get('blood-group', [DataController::class, 'bloodGroup']);
        Route::get('religion', [DataController::class, 'religion']);
        Route::get('employment-type', [DataController::class, 'employmentType']);
        Route::get('contract-type', [DataController::class, 'contractType']);
        Route::get('asset-status', [DataController::class, 'assetStatus']);
        Route::get('calendar-type', [DataController::class, 'calendarType']);
        Route::get('day',[DataController::class, 'day']);
        Route::get('english-month',[DataController::class, 'englishMonth']);
        Route::get('nepali-month',[DataController::class, 'nepaliMonth']);
        Route::get('country',[DataController::class, 'country']);
    });

    Route::prefix('branch')->group(function () {
        Route::get('list', [BranchController::class, 'index']);
        Route::post('store', [BranchController::class, 'store']);
        Route::get('show/{id}', [BranchController::class, 'show']);
        Route::post('update/{id}', [BranchController::class, 'update']);
        Route::post('update-manager/{id}', [BranchController::class, 'updateManager']);
        Route::post('destroy', [BranchController::class, 'destroy']);
        Route::get('trashed', [BranchController::class, 'trashed']);
        Route::post('restore', [BranchController::class, 'restore']);
        Route::post('force-delete', [BranchController::class, 'forceDelete']);
    });

    Route::prefix('department')->group(function () {
        Route::get('list', [DepartmentController::class, 'index']);
        Route::post('store', [DepartmentController::class, 'store']);
        Route::get('show/{id}', [DepartmentController::class, 'show']);
        Route::post('update/{id}', [DepartmentController::class, 'update']);
        Route::post('update-head/{id}', [DepartmentController::class, 'updateHead']);
        Route::post('destroy', [DepartmentController::class, 'destroy']);
        Route::get('trashed', [DepartmentController::class, 'trashed']);
        Route::post('restore', [DepartmentController::class, 'restore']);
        Route::post('force-delete', [DepartmentController::class, 'forceDelete']);
    });

    Route::prefix('department-employee')->group(function () {
        Route::get('list-by-department/{department_id}', [DepartmentEmployeeController::class, 'employeesByDepartment']);
        Route::get('list-by-branch/{department_id}', [DepartmentEmployeeController::class, 'employeesByBranch']);
        Route::post('store', [DepartmentEmployeeController::class, 'store']);
        Route::post('update/{id}', [DepartmentEmployeeController::class, 'update']);
        Route::post('destroy', [DepartmentEmployeeController::class, 'destroy']);
    });

    Route::prefix('employee')->group(function () {
        Route::get('list', [EmployeeController::class, 'index']);
        Route::post('store', [EmployeeController::class, 'store'])->middleware('idp_subscription_validation:employee');
        Route::get('show/{id}', [EmployeeController::class, 'show']);
        Route::post('update/{id}', [EmployeeController::class, 'update']);
        Route::post('update-image/{id}', [EmployeeController::class, 'updateImage']);
        Route::post('remove-image/{id}', [EmployeeController::class, 'removeImage']);
        Route::post('destroy', [EmployeeController::class, 'destroy']);
        Route::get('trashed', [EmployeeController::class, 'trashed']);
        Route::post('restore', [EmployeeController::class, 'restore']);
        Route::post('force-delete', [EmployeeController::class, 'forceDelete']);
        Route::post('import', [EmployeeController::class, 'employeeImport']);
        Route::get('download-sample', [EmployeeController::class, 'downloadSample']);

        Route::prefix('onboard')->group(function () {
            Route::post('store', [EmployeeOnboardingController::class, 'store']);
            Route::post('update/{id}', [EmployeeOnboardingController::class, 'update']);
            Route::post('destroy/{id}', [EmployeeOnboardingController::class, 'destroy']);
        });

        Route::prefix('contract')->group(function () {
            Route::post('store', [EmployeeContractController::class, 'store']);
            Route::post('update/{id}', [EmployeeContractController::class, 'update']);
            Route::post('destroy/{id}', [EmployeeContractController::class, 'destroy']);
        });

        Route::prefix('benefit')->group(function () {
            Route::post('store', [EmployeeBenefitController::class, 'store']);
            Route::post('update/{id}', [EmployeeBenefitController::class, 'update']);
        });

        Route::prefix('address')->group(function () {
            Route::post('store', [EmployeeAddressController::class, 'store']);
            Route::post('update/{id}', [EmployeeAddressController::class, 'update']);
        });

        Route::prefix('experience')->group(function () {
            Route::post('store', [EmployeeExperienceController::class, 'store']);
            Route::get('list/{employee_id}', [EmployeeExperienceController::class, 'index']);
            Route::post('update/{id}', [EmployeeExperienceController::class, 'update']);
            Route::post('destroy/{id}', [EmployeeExperienceController::class, 'destroy']);
        });

        Route::prefix('education')->group(function () {
            Route::post('store', [EmployeeEducationController::class, 'store']);
            Route::get('list/{employee_id}', [EmployeeEducationController::class, 'index']);
            Route::post('update/{id}', [EmployeeEducationController::class, 'update']);
            Route::post('destroy/{id}', [EmployeeEducationController::class, 'destroy']);
        });

        Route::prefix('family')->group(function () {
            Route::post('store', [EmployeeFamilyController::class, 'store']);
            Route::get('list/{employee_id}', [EmployeeFamilyController::class, 'index']);
            Route::post('update/{id}', [EmployeeFamilyController::class, 'update']);
            Route::post('destroy/{id}', [EmployeeFamilyController::class, 'destroy']);
        });
        Route::prefix('document')->group(function () {
            Route::post('store', [EmployeeDocumentController::class, 'store']);
        });

        Route::prefix('bank')->group(function () {
            Route::post('store', [EmployeeBankController::class, 'store']);
            Route::post('update/{id}', [EmployeeBankController::class, 'update']);
            Route::post('destroy/{id}', [EmployeeBankController::class, 'destroy']);
        });
    });

    Route::prefix('asset')->group(function () {
        Route::get('list', [AssetController::class, 'index']);
        Route::post('store', [AssetController::class, 'store'])->middleware('idp_subscription_validation:asset');;
        Route::get('show/{id}', [AssetController::class, 'show']);
        Route::post('update/{id}', [AssetController::class, 'update']);
        Route::post('destroy', [AssetController::class, 'destroy']);
        Route::post('update-image/{id}', [AssetController::class, 'updateImage']);
        Route::post('remove-image/{id}', [AssetController::class, 'removeImage']);
        Route::post('update-warranty-image/{id}', [AssetController::class, 'updateWarrantyImage']);
        Route::post('remove-warranty-image/{id}', [AssetController::class, 'removeWarrantyImage']);
        Route::post('update-guarantee-image/{id}', [AssetController::class, 'updateGuaranteeImage']);
        Route::post('remove-guarantee-image/{id}', [AssetController::class, 'removeGuaranteeImage']);

        Route::prefix('category')->group(function () {
            Route::get('list', [AssetCategoryController::class, 'index']);
            Route::post('store', [AssetCategoryController::class, 'store']);
            Route::post('update/{id}', [AssetCategoryController::class, 'update']);
            Route::get('show/{id}', [AssetCategoryController::class, 'show']);
            Route::post('destroy', [AssetCategoryController::class, 'destroy']);
        });

        Route::prefix('vendor')->group(function () {
            Route::get('list', [VendorController::class, 'index']);
            Route::post('store', [VendorController::class, 'store']);
            Route::get('show/{id}', [VendorController::class, 'show']);
            Route::post('update/{id}', [VendorController::class, 'update']);
            Route::post('destroy', [VendorController::class, 'destroy']);
        });

        Route::prefix('dispose')->group(function () {
            Route::get('list', [AssetDisposeController::class, 'index']);
            Route::post('store', [AssetDisposeController::class, 'store']);
            Route::get('show/{id}', [AssetDisposeController::class, 'show']);
            Route::post('update/{id}', [AssetDisposeController::class, 'update']);
            Route::post('destroy', [AssetDisposeController::class, 'destroy']);
        });

        Route::prefix('sale')->group(function () {
            Route::get('list', [AssetSaleController::class, 'index']);
            Route::post('store', [AssetSaleController::class, 'store']);
            Route::get('show/{id}', [AssetSaleController::class, 'show']);
            Route::post('update/{id}', [AssetSaleController::class, 'update']);
            Route::post('destroy', [AssetSaleController::class, 'destroy']);
        });

        Route::prefix('maintenance')->group(function () {
            Route::get('list', [AssetMaintenanceController::class, 'index']);
            Route::post('store', [AssetMaintenanceController::class, 'store']);
            Route::get('show/{id}', [AssetMaintenanceController::class, 'show']);
            Route::post('update/{id}', [AssetMaintenanceController::class, 'update']);
            Route::post('destroy', [AssetMaintenanceController::class, 'destroy']);
        });

        Route::prefix('usage')->group(function () {
            Route::get('list', [AssetUsageController::class, 'index']);
            Route::post('store', [AssetUsageController::class, 'store']);
            Route::get('show/{id}', [AssetUsageController::class, 'show']);
            Route::post('update/{id}', [AssetUsageController::class, 'update']);
            Route::post('destroy', [AssetUsageController::class, 'destroy']);
        });
    });

    Route::prefix('statistics')->group(function () {
        Route::get('company-count', [StatisticsController::class, 'getCompanyCounts']);
        Route::get('contract-count', [StatisticsController::class, 'getContractCounts']);
        Route::get('employee-count-by-branch', [StatisticsController::class, 'getEmployeeCountsByBranch']);
        Route::get('asset-count', [StatisticsController::class, 'getAssetCounts']);
        Route::get('asset-list', [StatisticsController::class, 'getAssetList']);
    });

    Route::prefix('company-profile')->group(function () {
        Route::post('upsert', [CompanyProfileController::class, 'upsert']);
        Route::get('show',[CompanyProfileController::class, 'showCompanyProfile']);
    });

    Route::prefix('company-holiday')->group(function () {
        Route::get('list', [CompanyHolidayController::class, 'index']);
        Route::post('store', [CompanyHolidayController::class, 'store']);
        Route::get('show/{id}', [CompanyHolidayController::class, 'show']);
        Route::post('update/{id}', [CompanyHolidayController::class, 'update']);
        Route::post('destroy', [CompanyHolidayController::class, 'destroy']);
    });

    Route::prefix('company-leave')->group(function () {
       Route::get('list', [CompanyLeaveController::class, 'index']);
       Route::post('store', [CompanyLeaveController::class, 'store']);
       Route::get('show/{id}', [CompanyLeaveController::class, 'show']);
       Route::post('update/{id}', [CompanyLeaveController::class, 'update']);
       Route::post('destroy', [CompanyLeaveController::class, 'destroy']);
    });

    Route::prefix('payroll-setting')->group(function () {
        Route::get('show',[PayrollSettingController::class, 'showPayrollSetting']);
        Route::post('upsert',[PayrollSettingController::class,'upsert']);
    });
});
