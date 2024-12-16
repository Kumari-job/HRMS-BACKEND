<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetSaleRequest;
use App\Models\AssetSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssetSaleController extends Controller
{
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

}
