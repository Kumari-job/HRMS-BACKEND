<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetSaleRequest;
use App\Http\Resources\AssetSaleResource;
use App\Models\AssetSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssetSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetSale::with('soldBy')->forCompany();
        $assetDisposes = $query->latest()->paginate($request->page_size ?? 10);
        return AssetSaleResource::collection($assetDisposes);
    }
    public function store(AssetSaleRequest $request)
    {
        try {
            $data = $request->validated();
            $assetSale = new AssetSale();
            $assetSale->fill($data);
            $assetSale->save();
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
    public function destroy($id)
    {
        try {
            $assetSale = AssetSale::forCompany()->find($id);
            if (!$assetSale) {
                return response()->json(['error' => true, 'message' => 'Asset sale not found.'], 404);
            }
            $assetSale->delete();
            return response()->json(['success' => true, 'message' => 'Asset sale deleted successfully.'], 200);
        }catch (\Exception $exception)
        {
            Log::error("Unable to delete asset sale " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to delete asset sale."],400);
        }
    }
}
