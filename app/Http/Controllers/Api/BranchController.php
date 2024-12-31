<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $query = Branch::where('company_id', $company_id);

        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if (in_array($key, ['id', 'company_id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }

        $branches = $query->where('company_id', $company_id)->latest()->paginate($request->page_size ?? 10);
        return BranchResource::collection($branches);

    }

    public function store(BranchRequest $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;

        if (Branch::where('company_id', $company_id)->where('name',$request->name)->exists()) {
            return response()->json(['error' => true, 'message' => 'Branch name already exists'], 422);
        }
        $established_date = $request->filled('established_date_nepali') ? DateHelper::nepaliToEnglish($request->established_date_nepali) : $request->established_date;

        $branch = new Branch($request->except('established_date'));
        $branch->established_date = $established_date;
        $branch->company_id = $company_id;
        $branch->save();
        return response()->json(['success'=>true, 'message' => 'Branch created successfully.'], 201);
    }

    public function show($branch_id)
    {
        $branch = Branch::find($branch_id);
        if (!$branch) {
            return response()->json(['error' => true, 'errors' => 'Branch not found.'], 404);
        }
        return new BranchResource($branch);
    }

    public function update(BranchRequest $request, $id)
    {
        $company_id = Auth::user()->selectedCompany->company_id;

        $branch = Branch::where('id', $id)->where('company_id', $company_id)->first();
        if (!$branch) {
            return response()->json(['error' => true, 'message' => 'Branch not found'], 404);
        }

        $branch->fill($request->all());
        $branch->save();

        return response()->json(['success' => true, 'message' => 'Branch updated successfully.'], 200);
    }
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        $ids = $request->ids;
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $branches = Branch::whereIn('id', $ids);
        $count = $branches->count();
        if ($count > 0) {
            $deleteStatus = $branches->delete();

            return response()->json(['success' => true, 'message' => 'Branches trashed successfully.'], 200);
        }
        return response()->json(['error' => true, 'message' => 'Branches not found.'], 400);
    }

    public function trashed(Request $request)
    {
        $query = Branch::onlyTrashed();
        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if (in_array($key, ['id', 'company_id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }
        $company_id = Auth::user()->selectedCompany->company_id;
        $branches = $query->where('company_id', $company_id)->latest()->paginate($request->page_size ?? 10);
        return BranchResource::collection($branches);
    }
    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $ids = $request->ids;
        Branch::withTrashed()->whereIn('id', $ids)->restore();
        return response()->json(['success' => true, 'message' => 'Branch restored successfully.'], 200);
    }

    public function forceDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        $ids = $request->ids;
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $branches = Branch::withTrashed()->whereIn('id', $ids);
        $count = $branches->count();
        if ($count > 0) {

            $branches->forceDelete();
            return response()->json(['success' => true, 'message' => 'Branches deleted successfully.'], 200);
        }
        return response()->json(['error' => true, 'message' => 'Branches not found.'], 404);
    }

}
