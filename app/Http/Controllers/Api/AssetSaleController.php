<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetSaleRequest;
use App\Http\Resources\AssetSaleResource;
use App\Models\Asset;
use App\Models\AssetSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssetSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetSale::with('soldBy','asset')->forCompany();
        $assetDisposes = $query->latest()->paginate($request->page_size ?? 10);
        return AssetSaleResource::collection($assetDisposes);
    }
    public function store(AssetSaleRequest $request)
    {
        try {
            $data = $request->validated();
            $asset = Asset::find($request->asset_id);
            if($asset->status == 'sold' || $asset->status == 'disposed'){
                return response()->json(['error'=>true,'message'=>'Asset is already '. $asset->status."."],403);
            }
            $assetSale = new AssetSale();
            $assetSale->fill($data);
            $assetSale->save();
            $asset->update(['status' => "sold"]);
            return response()->json(['success' => true, 'message' => 'Asset sale created successfully.'],201);
        }catch (\Exception $exception)
        {
            Log::error("Unable to store asset sale " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to store asset sale."],400);
        }
    }

    public function show($id)
    {
        $assetSale = AssetSale::with('soldBy','asset')->forCompany()->find($id);
        if (!$assetSale) {
            return response()->json(['error' => true, 'message' => 'Asset sale not found.'],404);
        }
        return new AssetSaleResource($assetSale);
    }
    public function update(AssetSaleRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $assetSale = AssetSale::forCompany()->find($id);
            if(!$assetSale){
                return response()->json(['error' => true, 'message' => 'Asset sale not found.'],404);
            }
            $assetSale->update($data);
            return response()->json(['success' => true, 'message' => 'Asset sale updated successfully.'],200);
        } catch (\Exception $exception)
        {
            Log::error("Unable to update asset sale " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to update asset sale."],400);
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
            $assetSales = AssetSale::forCompany()->whereIn('id', $ids);
            $count = $assetSales->count();
            if ($count > 0) {
                $deleteStatus = $assetSales->delete();

                return response()->json(['success' => true, 'message' => 'Asset Sales deleted successfully.'], 200);
            }
            return response()->json(['error' => true, 'message' => 'Asset Sales not found.'], 400);
        }catch (\Exception $exception){
            Log::error("Unable to delete asset sale " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to delete asset sale'], 400);
        }
    }
}
