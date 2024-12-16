<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetDisposeRequest;
use App\Http\Resources\AssetDisposeResource;
use App\Models\AssetDispose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssetDisposeController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $query = AssetDispose::with('disposedBy')->forCompany();
        $assetDisposes = $query->latest()->paginate($request->page_size ?? 10);
        return AssetDisposeResource::collection($assetDisposes);
    }

    public function store(AssetDisposeRequest $request)
    {
        try {

            $data = $request->validated();
            $assetDispose = new AssetDispose();
            $assetDispose->fill($data);
            $assetDispose->save();
            return response()->json(['success' => true, 'message' => 'Asset disposed Successfully'],201);
        } catch (\Exception $exception)
        {
            Log::error("Unable to dispose asset: ".$exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to dispose asset"],500);
        }
    }

    public function show($id)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $asset = AssetDispose::with('disposedBy','asset')->forCompany()->find($id);
        return new AssetDisposeResource($asset);
    }

    public function update(AssetDisposeRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $assetDispose = AssetDispose::forCompany()->find($id);
            if (!$assetDispose) {
                return response()->json(['success' => false, 'message' => 'Asset disposed not found'],404);
            }
            $assetDispose->update($data);
            return response()->json(['success' => true, 'message' => 'Asset disposed successfully'],201);
        }catch (\Exception $exception)
        {
            Log::error("Unable to update asset dispose: ".$exception->getMessage());
            return response()->json(['error' => true, 'message' => "Unable to dispose asset"],500);
        }
    }
    public function destroy($id)
    {
        $assetDispose = AssetDispose::forCompany()->find($id);
        if(!$assetDispose){
            return response()->json(['error' => true, 'message' => 'Asset disposed not found'],404);
        }
        $assetDispose->delete();
        return response()->json(['success' => true, 'message' => 'Asset disposed deleted successfully'],200);
    }

}
