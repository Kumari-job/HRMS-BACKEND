<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetCategoryRequest;
use App\Http\Resources\AssetCategoryResource;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssetCategoryController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $query = AssetCategory::query();

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
        $assetCategories = $query->where('company_id', $company_id)->latest()->paginate($request->page_size ?? 10);
        return AssetCategoryResource::collection($assetCategories);

    }

    public function store(AssetCategoryRequest $request)
    {
        try {
            $company_id = Auth::user()->selectedCompany->company_id;
            $assetCategory = new AssetCategory($request->all());
            $assetCategory->company_id = $company_id;
            $assetCategory->created_by = Auth::id();
            $assetCategory->save();
            return response()->json(['success' => true, 'message' => 'Asset Category created successfully'], 201);
        }catch (\Exception $exception){
            Log::error("Unable to create asset category " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to create asset category'], 500);
        }
    }

    public function show($id)
    {
        $company_id =  Auth::user()->selectedCompany->company_id;
        $assetCategory = AssetCategory::where('company_id',$company_id)->find($id);
        if(!$assetCategory){
            return response()->json(['error' => true, 'message' => 'Asset Category not found'], 404);
        }
        return new AssetCategoryResource($assetCategory);
    }
}
