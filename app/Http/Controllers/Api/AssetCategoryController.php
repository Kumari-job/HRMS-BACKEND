<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetCategoryRequest;
use App\Http\Resources\AssetCategoryResource;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            if(AssetCategory::where('title',$request->title)->where('company_id',$company_id)->exists()){
                return response()->json(['error'=>true, 'message'=>'Asset Category already exists.'],403);
            }
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
        $assetCategory = AssetCategory::with('createdBy','updatedBy')->where('company_id',$company_id)->find($id);
        if(!$assetCategory){
            return response()->json(['error' => true, 'message' => 'Asset Category not found'], 404);
        }

        return new AssetCategoryResource($assetCategory);
    }

    public function update(AssetCategoryRequest $request, $id)
    {
        try {
            $company_id = Auth::user()->selectedCompany->company_id;

            $assetCategory = AssetCategory::find($id);
            if (!$assetCategory) {
                return response()->json(['error' => true, 'message' => 'Asset Category not found'], 404);
            }
            if(AssetCategory::where('title',$request->title)->where('company_id',$company_id)->where('id','!=',$id)->exists()){
                return response()->json(['error'=>true, 'message'=>'Asset Category already exists.'],403);
            }
            $assetCategory->updated_by = Auth::id();
            $assetCategory->update($request->all());
            return response()->json(['success' => true, 'message' => 'Asset Category updated successfully'], 200);
        }catch (\Exception $exception){
            Log::error("Unable to update asset category " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to update asset category'], 400);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'array'
            ]);
            $ids = $request->ids;
            if ($validator->fails()) {
                return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
            }
            $assetCategories = AssetCategory::whereIn('id', $ids);
            $count = $assetCategories->count();
            if ($count > 0) {
                $deleteStatus = $assetCategories->delete();

                return response()->json(['success' => true, 'message' => 'Asset Categories deleted successfully.'], 200);
            }
            return response()->json(['error' => true, 'message' => 'Asset Categories not found.'], 400);
        }catch (\Exception $exception){
            Log::error("Unable to delete asset category " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to delete asset category'], 400);
        }
    }
}
