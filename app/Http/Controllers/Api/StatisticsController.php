<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getContractCounts()
    {
        $contract_counts = EmployeeContract::forCompany()
            ->select('contract_type', DB::raw('COUNT(*) as count'))
            ->groupBy('contract_type')
            ->get();

        return response()->json([
            'contract_counts' => $contract_counts
        ], 200);
    }

    public function getEmployeeCountsByBranch()
    {
        $branch_count = Branch::count();
        if($branch_count <= 1){
            $employee_counts = Department::forCompany()
                ->withCount('employees')
                ->get(['id', 'name', 'employees_count'])
                ->map(function ($department) {
                    return [
                        'department_id' => $department->id,
                        'department_name' => $department->name,
                        'employee_count' => $department->employees_count,
                    ];
                });
            return response()->json([
                'has_many_branches' => false,
                'employee_counts' => $employee_counts
            ]);
        }
        $employee_counts = Branch::query()
            ->with(['departments' => function ($query) {
                $query->withCount('employees');
            }])
            ->get()
            ->map(function ($branch) {
                $total_employees = $branch->departments->sum('employees_count');
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
    public function getAssetCounts()
    {
        $asset_counts = Asset::forCompany()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'asset_counts' => $asset_counts
        ], 200);
    }
}
