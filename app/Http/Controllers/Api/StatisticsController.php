<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\DateRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StatisticsController extends Controller
{
    public function getCompanyCounts()
    {
        $branch_count = Branch::count();
        $department_count = Department::forCompany()->count();
        $employees_count = Employee::count();
        $asset_count = Asset::forCompany()->count();
        $vendor_count = Vendor::count();
        return response()->json([
            'branch_count' => $branch_count,
            'employees_count' => $employees_count,
            'department_count'=> $department_count,
            'asset_count' => $asset_count,
            'vendor_count' => $vendor_count,
        ],200);
    }

    public function getContractCounts(DateRequest $request)
    {
        $start_date =  $request->filled('start_date_nepali') ? DateHelper::nepaliToEnglish($request->start_date_nepali) : $request->start_date ?? null;
        $end_date = $request->filled('end_date_nepali') ? DateHelper::nepaliToEnglish($request->end_date_nepali) : $request->end_date ?? null;

        $contract_counts = EmployeeContract::forCompany()
            ->where(function ($query) use ($start_date, $end_date) {
                if ($start_date) {
                    $query->whereDate('created_at', '>=', $start_date);
                }
                if ($end_date) {
                    $query->whereDate('created_At', '<=', $end_date);
                }
            })
            ->select('contract_type', DB::raw('COUNT(*) as count'))
            ->groupBy('contract_type')
            ->get();

        return response()->json([
            'contract_counts' => $contract_counts
        ], 200);
    }

    public function getEmployeeCountsByBranch(DateRequest $request)
    {
        $start_date =  $request->filled('start_date_nepali') ? DateHelper::nepaliToEnglish($request->start_date_nepali) : $request->start_date ?? null;
        $end_date = $request->filled('end_date_nepali') ? DateHelper::nepaliToEnglish($request->end_date_nepali) : $request->end_date ?? null;

        $branch_count = Branch::count();
        if($branch_count <= 1){
            $employee_counts = Department::forCompany()
                ->with(['employees' => function ($query) use ($start_date, $end_date) {
                    if ($start_date) {
                        $query->where('department_employees.joined_at', '>=', $start_date);
                    }
                    if ($end_date) {
                        $query->where('department_employees.joined_at', '<=', $end_date);
                    }
                }])
                ->get()
                ->map(function ($department) {
                    $employee_count = $department->employees->count();
                    return [
                        'department_id' => $department->id,
                        'department_name' => $department->name,
                        'employee_count' => $employee_count,
                    ];
                });


            return response()->json([
                'has_many_branches' => false,
                'employee_counts' => $employee_counts
            ]);
        }
        $employee_counts = Branch::query()
            ->with(['departments' => function ($query) use ($start_date, $end_date) {
                $query->with(['employees' => function ($employeeQuery) use ($start_date, $end_date) {
                    $employeeQuery->where(function ($subQuery) use ($start_date, $end_date) {
                        if ($start_date) {
                            $subQuery->where('department_employees.joined_at', '>=', $start_date);
                        }
                        if ($end_date) {
                            $subQuery->where('department_employees.joined_at', '<=', $end_date);
                        }
                    });
                }]);
            }])
            ->get()
            ->map(function ($branch) {
                $total_employees = $branch->departments->reduce(function ($carry, $department) {
                    return $carry + $department->employees->count();
                }, 0);
                return [
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->name,
                    'employee_count' => $total_employees,
                ];
            });

        return response()->json([
            'has_many_branches' => true,
            'employee_counts' => $employee_counts
        ], 200);
    }
    public function getAssetCounts(DateRequest $request)
    {

        $start_date =  $request->filled('start_date_nepali') ? DateHelper::nepaliToEnglish($request->start_date_nepali) : $request->start_date ?? null;
        $end_date = $request->filled('end_date_nepali') ? DateHelper::nepaliToEnglish($request->end_date_nepali) : $request->end_date ?? null;

        $asset_counts = Asset::forCompany()
            ->where(function ($query) use ($start_date, $end_date) {
                if ($start_date) {
                    $query->where('purchased_at', '>=', $start_date);
                }
                if ($end_date) {
                    $query->where('purchased_at', '<=', $end_date);
                }
            })
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'asset_counts' => $asset_counts
        ], 200);
    }

    public function getAssetList()
    {

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $assets = Asset::forCompany()->whereBetween('purchased_at', [$startOfMonth, $endOfMonth])->latest()->take(5)->get();

        return AssetResource::collection($assets);
    }
}
