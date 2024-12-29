<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetUsageRequest;
use App\Http\Resources\AssetUsageResource;
use App\Models\AssetUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssetUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetUsage::with('assignedBy', 'employee:id,name', 'asset')->forCompany();
        $assetUsages = $query->latest()->paginate($request->page_size ?? 10);
        return AssetUsageResource::collection($assetUsages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssetUsageRequest $request)
    {
        try {
            $data = $request->validated();
            $assigned_at = $request->filled('assigned_at_nepali') ? DateHelper::nepaliToEnglish($request->assigned_at_nepali) : $request->assigned_at;
            $assigned_end_at = $request->filled('assigned_end_at_nepali') ? DateHelper::nepaliToEnglish($request->assigned_end_at_nepali) : $request->assigned_end_at;
            $data['assigned_end_at'] = $assigned_end_at;
            $data['assigned_at'] = $assigned_at;
            $assetUsage = new AssetUsage();
            $assetUsage->fill($data);
            $assetUsage->save();
            return response()->json(['success' => true, 'message' => 'Asset usage created successfully'], 201);
        } catch (\Exception $exception) {
            Log::error("Unable to store asset usage: {$exception->getMessage()}");
            return response()->json(['error' => true, 'message' => "Unable to enter asset usage"], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assetUsage = AssetUsage::with('assignedBy', 'employee', 'asset')->forCompany()->findOrFail($id);
        if (!$assetUsage) {
            return response()->json(['error' => true, 'message' => 'Asset usage not found'], 404);
        }
        return new AssetUsageResource($assetUsage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssetUsageRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $assigned_at = $request->filled('assigned_at_nepali') ? DateHelper::nepaliToEnglish($request->assigned_at_nepali) : $request->assigned_at;
            $assigned_end_at = $request->filled('assigned_end_at_nepali') ? DateHelper::nepaliToEnglish($request->assigned_end_at_nepali) : $request->assigned_end_at_nepali;
            $data['assigned_end_at'] = $assigned_end_at;
            $data['assigned_at'] = $assigned_at;
            $assetUsage = AssetUsage::forCompany()->find($id);
            if (!$assetUsage) {
                return response()->json(['error' => true, 'message' => 'Asset usage not found'], 404);
            }
            $assetUsage->update($data);
            return response()->json(['success' => true, 'message' => 'Asset usage updated successfully'], 200);
        } catch (\Exception $exception) {
            Log::error("Unable to update asset usage: {$exception->getMessage()}");
            return response()->json(['error' => true, 'message' => "Unable to update asset usage"], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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
            $assetUsages = AssetUsage::forCompany()->whereIn('id', $ids);
            $count = $assetUsages->count();
            if ($count > 0) {
                $deleteStatus = $assetUsages->delete();

                return response()->json(['success' => true, 'message' => 'Asset usages deleted successfully.'], 200);
            }
            return response()->json(['error' => true, 'message' => 'Asset usages not found.'], 400);
        } catch (\Exception $exception) {
            Log::error("Unable to delete asset usages " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to delete asset usages'], 400);
        }
    }
}
