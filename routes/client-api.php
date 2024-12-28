<?php

use App\Http\Controllers\Api\StatisticsController;
use Illuminate\Support\Facades\Route;


Route::prefix('statistics')->group(function () {
    Route::get('company-count',[StatisticsController::class, 'getCompanyCounts']);
    Route::get('contract-count',[StatisticsController::class, 'getContractCounts']);
    Route::get('employee-count-by-branch',[StatisticsController::class, 'getEmployeeCountsByBranch']);
    Route::get('asset-count',[StatisticsController::class, 'getAssetCounts']);
    Route::get('asset-list',[StatisticsController::class, 'getAssetList']);
});