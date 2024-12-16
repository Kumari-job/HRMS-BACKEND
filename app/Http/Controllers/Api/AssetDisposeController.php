<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetDisposeRequest;
use App\Models\AssetDispose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssetDisposeController extends Controller
{
    public function index()
    {

    }

    public function store(AssetDisposeRequest $request)
    {
        try {

            $data = $request->validated();
            $assetDispose = new AssetDispose();
            $assetDispose->fill($data);
            $assetDispose->disposed_by = Auth::id();
            $assetDispose->save();
            return response()->json(['success' => true, 'message' => 'Asset Disposed Successfully'],201);
        } catch (\Exception $exception)
        {
            Log::error("Unable to dispose asset: ".$exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to dispose asset"],500);
        }
    }

    public function update(AssetDisposeRequest $request, $id)
    {

    }
}
