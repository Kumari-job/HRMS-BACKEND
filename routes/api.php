<?php

use App\Http\Controllers\Api\AssetCategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AssetDisposeController;
use App\Http\Controllers\Api\AssetMaintenanceController;
use App\Http\Controllers\Api\AssetSaleController;
use App\Http\Controllers\Api\AssetUsageController;
use App\Http\Controllers\Api\AttendanceController;
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
use App\Http\Controllers\Api\EmployeeLeaveController;
use App\Http\Controllers\Api\EmployeeLeaveStatusController;
use App\Http\Controllers\Api\EmployeeOnboardingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PayrollSettingController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\SelectedCompanyController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\EmployeeAuth\AuthenticationController as EmployeeAuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserRoleController;
use App\Http\Middleware\VerifyCommonToken;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// temp 
Route::post('users/store-idp-user-id', [AuthenticationController::class, 'storeIdpUserID'])->middleware('verify_common_token');
Route::post('users/generate-access-token', [AuthenticationController::class, 'generateAccessToken'])->middleware('verify_common_token');
Route::post('employee-login',[EmployeeAuthController::class, 'login']);
Route::post('employee-forgot-password', [EmployeeAuthController::class,'forgotPassword']);
Route::post('employee-verify-token', [EmployeeAuthController::class, 'verifyToken']);
Route::post('employee-verify-otp', [EmployeeAuthController::class, 'verifyOTP']);
Route::post('employee-reset-password',[EmployeeAuthController::class,'resetPassword']);
// client sync and access token
Route::group(['middleware' => 'client'], function () {
    Route::post('sync-user', [AuthenticationController::class, 'syncIdpUser']);
    Route::post('access-token', [AuthenticationController::class, 'generateAccessToken']);
});


Route::group(['middleware' => ['auth:api']], function () {
    Route::post('employee-logout',[EmployeeAuthController::class,'logout'])->withoutMiddleware('is_employee_password_changed');
    Route::post('logout', [AuthenticationController::class, 'logout']);
    Route::post('employee-change-password',[EmployeeAuthController::class, 'changePassword'])->withoutMiddleware('is_employee_password_changed');
    Route::prefix('user')->group(function () {
        Route::get('list', [UserController::class, 'index']);
        Route::get('profile', [UserController::class, 'profile'])->withoutMiddleware('is_employee_password_changed');
        Route::post('migrate-employee-data',[UserController::class, 'migrateEmployeeData']);
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
        Route::get('leave-type', [DataController::class, 'leaveType']);
    });

    Route::prefix('branch')->group(function () {
        Route::get('list', [BranchController::class, 'index'])->middleware('permission_check:view_branch');
        Route::post('store', [BranchController::class, 'store'])->middleware('permission_check:create_branch');
        Route::get('show/{id}', [BranchController::class, 'show'])->middleware('permission_check:show_branch');
        Route::post('update/{id}', [BranchController::class, 'update'])->middleware('permission_check:edit_branch');
        Route::post('update-manager/{id}', [BranchController::class, 'updateManager'])->middleware('permission_check:edit_branch');
        Route::post('destroy', [BranchController::class, 'destroy'])->middleware('permission_check:delete_branch');
        Route::get('trashed', [BranchController::class, 'trashed'])->middleware('permission_check:delete_branch');
        Route::post('restore', [BranchController::class, 'restore'])->middleware('permission_check:delete_branch');
        Route::post('force-delete', [BranchController::class, 'forceDelete'])->middleware('permission_check:delete_branch');
    });

    Route::prefix('department')->group(function () {
        Route::get('list', [DepartmentController::class, 'index'])->middleware('permission_check:view_department');
        Route::post('store', [DepartmentController::class, 'store'])->middleware('permission_check:create_department');
        Route::get('show/{id}', [DepartmentController::class, 'show'])->middleware('permission_check:view_department');
        Route::post('update/{id}', [DepartmentController::class, 'update'])->middleware('permission_check:edit_department');
        Route::post('update-head/{id}', [DepartmentController::class, 'updateHead'])->middleware('permission_check:edit_department');
        Route::post('destroy', [DepartmentController::class, 'destroy'])->middleware('permission_check:delete_department');
        Route::get('trashed', [DepartmentController::class, 'trashed'])->middleware('permission_check:delete_department');
        Route::post('restore', [DepartmentController::class, 'restore'])->middleware('permission_check:delete_department');
        Route::post('force-delete', [DepartmentController::class, 'forceDelete'])->middleware('permission_check:delete_department');
    });

    Route::prefix('department-employee')->group(function () {
        Route::get('list-by-department/{department_id}', [DepartmentEmployeeController::class, 'employeesByDepartment'])->middleware('permission_check:view_department_employee');
        Route::get('list-by-branch/{department_id}', [DepartmentEmployeeController::class, 'employeesByBranch'])->middleware('permission_check:view_department_employee');
        Route::post('store', [DepartmentEmployeeController::class, 'store'])->middleware('permission_check:create_department_employee');
        Route::post('update/{id}', [DepartmentEmployeeController::class, 'update'])->middleware('permission_check:edit_department_employee');
        Route::post('destroy', [DepartmentEmployeeController::class, 'destroy'])->middleware('permission_check:delete_department_employee');
    });

    Route::prefix('employee')->group(function () {
        Route::get('list', [EmployeeController::class, 'index'])->middleware('permission_check:view_employee');
        Route::post('store', [EmployeeController::class, 'store'])->middleware(['idp_subscription_validation:employee'])->middleware('permission_check:create_employee');
        Route::get('show/{id}', [EmployeeController::class, 'show'])->middleware('permission_check:view_employee');
        Route::post('update/{id}', [EmployeeController::class, 'update'])->middleware('permission_check:edit_employee');
        Route::post('update-image/{id}', [EmployeeController::class, 'updateImage'])->middleware('permission_check:edit_employee');
        Route::post('remove-image/{id}', [EmployeeController::class, 'removeImage'])->middleware('permission_check:edit_employee');
        Route::post('destroy', [EmployeeController::class, 'destroy'])->middleware('permission_check:delete_employee');
        Route::get('trashed', [EmployeeController::class, 'trashed'])->middleware('permission_check:delete_employee');
        Route::post('restore', [EmployeeController::class, 'restore'])->middleware('permission_check:delete_employee');
        Route::post('force-delete', [EmployeeController::class, 'forceDelete'])->middleware('permission_check:delete_employee');
        Route::post('import', [EmployeeController::class, 'employeeImport'])->middleware('permission_check:create_employee');
        Route::get('download-sample', [EmployeeController::class, 'downloadSample']);

        Route::prefix('onboard')->group(function () {
            Route::post('store', [EmployeeOnboardingController::class, 'store'])->middleware('permission_check:manage_employee_details');
            Route::post('update/{id}', [EmployeeOnboardingController::class, 'update'])->middleware('permission_check:manage_employee_details');
            Route::post('destroy/{id}', [EmployeeOnboardingController::class, 'destroy'])->middleware('permission_check:manage_employee_details');
        });

        Route::prefix('leave')->group(function (){
           Route::get('list',[EmployeeLeaveController::class, 'index']);
           Route::get('my-leave',[EmployeeLeaveController::class, 'showUsersLeaves']);
           Route::post('store', [EmployeeLeaveController::class, 'store']);
           Route::prefix('status')->group(function (){
               Route::get('pending',[EmployeeLeaveStatusController::class, 'listPendingLeaves']);
               Route::post('change-status/{id}',[EmployeeLeaveStatusController::class, 'changeStatus']);
           });
        });

        Route::prefix('contract')->group(function () {
            Route::post('store', [EmployeeContractController::class, 'store'])->middleware('permission_check:manage_employee_details');
            Route::post('update/{id}', [EmployeeContractController::class, 'update'])->middleware('permission_check:manage_employee_details');
            Route::post('destroy/{id}', [EmployeeContractController::class, 'destroy'])->middleware('permission_check:manage_employee_details');
        });

        Route::prefix('benefit')->group(function () {
            Route::post('store', [EmployeeBenefitController::class, 'store'])->middleware('permission_check:manage_employee_details');
            Route::post('update/{id}', [EmployeeBenefitController::class, 'update'])->middleware('permission_check:manage_employee_details');
        });

        Route::prefix('address')->group(function () {
            Route::post('store', [EmployeeAddressController::class, 'store'])->middleware('permission_check:manage_employee_details');
            Route::post('update/{id}', [EmployeeAddressController::class, 'update'])->middleware('permission_check:manage_employee_details');
        });

        Route::prefix('experience')->group(function () {
            Route::post('store', [EmployeeExperienceController::class, 'store'])->middleware('permission_check:manage_employee_details');
            Route::get('list/{employee_id}', [EmployeeExperienceController::class, 'index'])->middleware('permission_check:manage_employee_details');
            Route::post('update/{id}', [EmployeeExperienceController::class, 'update'])->middleware('permission_check:manage_employee_details');
            Route::post('destroy/{id}', [EmployeeExperienceController::class, 'destroy'])->middleware('permission_check:manage_employee_details');
        });

        Route::prefix('education')->group(function () {
            Route::post('store', [EmployeeEducationController::class, 'store'])->middleware('permission_check:manage_employee_details');
            Route::get('list/{employee_id}', [EmployeeEducationController::class, 'index'])->middleware('permission_check:manage_employee_details');
            Route::post('update/{id}', [EmployeeEducationController::class, 'update'])->middleware('permission_check:manage_employee_details');
            Route::post('destroy/{id}', [EmployeeEducationController::class, 'destroy'])->middleware('permission_check:manage_employee_details');
        });

        Route::prefix('family')->group(function () {
            Route::post('store', [EmployeeFamilyController::class, 'store'])->middleware('permission_check:manage_employee_details');
            Route::get('list/{employee_id}', [EmployeeFamilyController::class, 'index'])->middleware('permission_check:manage_employee_details');
            Route::post('update/{id}', [EmployeeFamilyController::class, 'update'])->middleware('permission_check:manage_employee_details');
            Route::post('destroy/{id}', [EmployeeFamilyController::class, 'destroy'])->middleware('permission_check:manage_employee_details');
        });
        Route::prefix('document')->group(function () {
            Route::post('store', [EmployeeDocumentController::class, 'store'])->middleware('permission_check:manage_employee_details');
        });

        Route::prefix('bank')->group(function () {
            Route::post('store', [EmployeeBankController::class, 'store'])->middleware('permission_check:manage_employee_details');
            Route::post('update/{id}', [EmployeeBankController::class, 'update'])->middleware('permission_check:manage_employee_details');
            Route::post('destroy/{id}', [EmployeeBankController::class, 'destroy'])->middleware('permission_check:manage_employee_details');
        });
    });

    Route::prefix('notification')->group(function () {
        Route::get('/list', [NotificationController::class, 'index']);
        Route::get('/list/read-notifications', [NotificationController::class, 'readNotifications']);
        Route::get('/list/unread-notifications', [NotificationController::class, 'unReadNotifications']);

        Route::get('/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::get('/mark-as-read/{id}', [NotificationController::class, 'markSingleRead']);
        Route::get('/mark-all-unread', [NotificationController::class, 'markAllUnread']);
        Route::get('/mark-as-unread/{id}', [NotificationController::class, 'markSingleUnread']);
        Route::get('/clear-all', [NotificationController::class, 'clearAll']);
        Route::get('/clear-single/{id}', [NotificationController::class, 'notificationDelete']);
    });

    Route::prefix('role')->group(function () {
        Route::get('list', [RoleController::class, 'index'])->middleware('permission_check:manage_role');
        Route::post('store', [RoleController::class, 'store'])->middleware('permission_check:manage_role');
        Route::post('update/{id}', [RoleController::class, 'update'])->middleware('permission_check:manage_role');
        Route::post('destroy', [RoleController::class, 'destroy'])->middleware('permission_check:manage_role');
    });


    Route::prefix('role-permission')->group(function () {
        Route::post('store', [RolePermissionController::class, 'store'])->middleware('permission_check:manage_role');
        Route::get('permissions-by-role/{id}', [RolePermissionController::class, 'permissionsByRole'])->middleware('permission_check:manage_role');
    });

    Route::prefix('user-role')->group(function () {
        Route::get('/list/{user_id}', [UserRoleController::class, 'index'])->middleware('permission_check:manage_role');
        Route::get('user-role-by-company', [UserRoleController::class, 'userRolesByCompany'])->middleware('permission_check:manage_role');
        Route::post('store', [UserRoleController::class, 'store'])->middleware('permission_check:manage_role');
        Route::post('destroy/{user_id}', [UserRoleController::class, 'destroy'])->middleware('permission_check:manage_role');
    });

    Route::prefix('attendance')->group(function () {
        Route::get('list', [AttendanceController::class, 'index']);
        Route::post('store', [AttendanceController::class, 'store'])->middleware('permission_check:manage_attendance');
        Route::get('daily', [AttendanceController::class, 'dailyAttendance']);
        Route::post('punch-in', [AttendanceController::class, 'punchIn']);
        Route::post('punch-out', [AttendanceController::class, 'punchOut']);
    });
    Route::prefix('asset')->group(function () {
        Route::get('list', [AssetController::class, 'index'])->middleware('permission_check:view_asset');
        Route::post('store', [AssetController::class, 'store'])->middleware('idp_subscription_validation:asset')->middleware('permission_check:create_asset');
        Route::get('show/{id}', [AssetController::class, 'show'])->middleware('permission_check:view_asset');
        Route::post('update/{id}', [AssetController::class, 'update'])->middleware('permission_check:edit_asset');
        Route::post('destroy', [AssetController::class, 'destroy'])->middleware('permission_check:delete_asset');
        Route::post('update-image/{id}', [AssetController::class, 'updateImage'])->middleware('permission_check:edit_asset');
        Route::post('remove-image/{id}', [AssetController::class, 'removeImage'])->middleware('permission_check:edit_asset');
        Route::post('update-warranty-image/{id}', [AssetController::class, 'updateWarrantyImage'])->middleware('permission_check:edit_asset');
        Route::post('remove-warranty-image/{id}', [AssetController::class, 'removeWarrantyImage'])->middleware('permission_check:edit_asset');
        Route::post('update-guarantee-image/{id}', [AssetController::class, 'updateGuaranteeImage'])->middleware('permission_check:edit_asset');
        Route::post('remove-guarantee-image/{id}', [AssetController::class, 'removeGuaranteeImage'])->middleware('permission_check:edit_asset');

        Route::prefix('category')->group(function () {
            Route::get('list', [AssetCategoryController::class, 'index']);
            Route::post('store', [AssetCategoryController::class, 'store'])->middleware('permission_check:manage_asset');
            Route::post('update/{id}', [AssetCategoryController::class, 'update'])->middleware('permission_check:manage_asset');
            Route::get('show/{id}', [AssetCategoryController::class, 'show']);
            Route::post('destroy', [AssetCategoryController::class, 'destroy'])->middleware('permission_check:manage_asset');
        });

        Route::prefix('vendor')->group(function () {
            Route::get('list', [VendorController::class, 'index']);
            Route::post('store', [VendorController::class, 'store'])->middleware('permission_check:manage_asset');
            Route::get('show/{id}', [VendorController::class, 'show']);
            Route::post('update/{id}', [VendorController::class, 'update'])->middleware('permission_check:manage_asset');
            Route::post('destroy', [VendorController::class, 'destroy'])->middleware('permission_check:manage_asset');
        });

        Route::prefix('dispose')->group(function () {
            Route::get('list', [AssetDisposeController::class, 'index']);
            Route::post('store', [AssetDisposeController::class, 'store'])->middleware('permission_check:manage_asset');
            Route::get('show/{id}', [AssetDisposeController::class, 'show']);
            Route::post('update/{id}', [AssetDisposeController::class, 'update'])->middleware('permission_check:manage_asset');
            Route::post('destroy', [AssetDisposeController::class, 'destroy'])->middleware('permission_check:manage_asset');
        });

        Route::prefix('sale')->group(function () {
            Route::get('list', [AssetSaleController::class, 'index']);
            Route::post('store', [AssetSaleController::class, 'store'])->middleware('permission_check:manage_asset');
            Route::get('show/{id}', [AssetSaleController::class, 'show']);
            Route::post('update/{id}', [AssetSaleController::class, 'update'])->middleware('permission_check:manage_asset');
            Route::post('destroy', [AssetSaleController::class, 'destroy'])->middleware('permission_check:manage_asset');
        });

        Route::prefix('maintenance')->group(function () {
            Route::get('list', [AssetMaintenanceController::class, 'index']);
            Route::post('store', [AssetMaintenanceController::class, 'store'])->middleware('permission_check:manage_asset');
            Route::get('show/{id}', [AssetMaintenanceController::class, 'show']);
            Route::post('update/{id}', [AssetMaintenanceController::class, 'update'])->middleware('permission_check:manage_asset');
            Route::patch('toggle-status/{id}', [AssetMaintenanceController::class, 'toggleMaintenanceStatus'])->middleware('permission_check:manage_asset');
            Route::post('destroy', [AssetMaintenanceController::class, 'destroy'])->middleware('permission_check:manage_asset');
        });

        Route::prefix('usage')->group(function () {
            Route::get('list', [AssetUsageController::class, 'index']);
            Route::post('store', [AssetUsageController::class, 'store'])->middleware('permission_check:manage_asset');
            Route::get('show/{id}', [AssetUsageController::class, 'show']);
            Route::post('update/{id}', [AssetUsageController::class, 'update'])->middleware('permission_check:manage_asset');
            Route::patch('toggle-status/{id}', [AssetUsageController::class, 'toggleUsageStatus'])->middleware('permission_check:manage_asset');
            Route::post('destroy', [AssetUsageController::class, 'destroy'])->middleware('permission_check:manage_asset');
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
        Route::post('upsert', [CompanyProfileController::class, 'upsert'])->middleware('permission_check:manage_company_profile');
        Route::get('show',[CompanyProfileController::class, 'showCompanyProfile'])->middleware('permission_check:view_company_profile');
        Route::get('country-policy',[CompanyProfileController::class, 'showCompanyCountryPolicy']);
    });

    Route::prefix('company-holiday')->group(function () {
        Route::get('list', [CompanyHolidayController::class, 'index'])->middleware('permission_check:view_holiday');
        Route::post('store', [CompanyHolidayController::class, 'store'])->middleware('permission_check:create_holiday');
        Route::get('show/{id}', [CompanyHolidayController::class, 'show'])->middleware('permission_check:view_holiday');
        Route::post('update/{id}', [CompanyHolidayController::class, 'update'])->middleware('permission_check:edit_holiday');
        Route::post('destroy', [CompanyHolidayController::class, 'destroy'])->middleware('permission_check:delete_holiday');
    });

    Route::prefix('company-leave')->group(function () {
       Route::get('list', [CompanyLeaveController::class, 'index'])->middleware('permission_check:view_leave');
       Route::post('store', [CompanyLeaveController::class, 'store'])->middleware('permission_check:create_leave');
       Route::get('show/{id}', [CompanyLeaveController::class, 'show'])->middleware('permission_check:view_leave');
       Route::post('update/{id}', [CompanyLeaveController::class, 'update'])->middleware('permission_check:edit_leave');
       Route::post('destroy', [CompanyLeaveController::class, 'destroy'])->middleware('permission_check:delete_leave');
    });

    Route::prefix('payroll-setting')->group(function () {
        Route::get('show',[PayrollSettingController::class, 'showPayrollSetting'])->middleware('permission_check:view_payroll');
        Route::post('upsert',[PayrollSettingController::class,'upsert'])->middleware('permission_check:manage_payroll');
    });
});
