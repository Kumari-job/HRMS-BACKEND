<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetDisposeRequest;
use App\Http\Resources\AssetDisposeResource;
use App\Models\AssetDispose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssetDisposeController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $query = AssetDispose::with('disposedBy','asset')->forCompany();
        $assetDisposes = $query->latest()->paginate($request->page_size ?? 10);
        return AssetDisposeResource::collection($assetDisposes);
    }

    public function store(AssetDisposeRequest $request)
    {
        try {
            $data = $request->except('disposed_at_nepali','disposed_at');
            $disposed_at = $request->filled('disposed_at_nepali') ? DateHelper::nepaliToEnglish($request->disposed_at_nepali) : $request->disposed_at;
            $data['disposed_at'] = $disposed_at;
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
        if (!$asset) {
            return response()->json(['error' => true, 'message' => 'Asset Dispose not found'],404);
        }
        return new AssetDisposeResource($asset);
    }

    public function update(AssetDisposeRequest $request, $id)
    {
        try {
            $data = $request->except('disposed_at_nepali','disposed_at');
            $disposed_at = $request->filled('disposed_at_nepali') ? DateHelper::nepaliToEnglish($request->disposed_at_nepali) : $request->disposed_at;
            $data['disposed_at'] = $disposed_at;
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
            $assetDisposes = AssetDispose::forCompany()->whereIn('id', $ids);
            $count = $assetDisposes->count();
            if ($count > 0) {
                $deleteStatus = $assetDisposes->delete();

                return response()->json(['success' => true, 'message' => 'Asset Disposes deleted successfully.'], 200);
            }
            return response()->json(['error' => true, 'message' => 'Asset Disposes not found.'], 400);
        }catch (\Exception $exception){
            Log::error("Unable to delete asset dispose " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to delete asset dispose'], 400);
        }
    }

}
