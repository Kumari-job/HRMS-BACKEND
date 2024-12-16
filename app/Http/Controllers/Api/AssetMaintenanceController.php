<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetMaintenanceRequest;
use App\Http\Resources\AssetMaintenanceResource;
use App\Models\AssetMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssetMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetMaintenance::with('createdBy','updatedBy')->forCompany();
        $assetDisposes = $query->latest()->paginate($request->page_size ?? 10);
        return AssetMaintenanceResource::collection($assetDisposes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssetMaintenanceRequest $request)
    {
        try {
            $data = $request->validated();
            $assetMaintenance = new AssetMaintenance();
            $assetMaintenance->fill($data);
            $assetMaintenance->created_by = Auth::id();
            $assetMaintenance->save();
            return response()->json(['success' => true, 'message' => 'Asset Maintenance added successfully'],201);
        } catch (\Exception $exception)
        {
            Log::error("Unable to added asset maintenance: " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to add asset maintenance'],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assetMaintenance = AssetMaintenance::with('asset','createdBy','updatedBy')->forCompany()->find($id);
        if(!$assetMaintenance)
        {
            return response()->json(['error' => true, 'message' => 'Asset Maintenance not found'],404);
        }
        return new AssetMaintenanceResource($assetMaintenance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssetMaintenanceRequest $request, string $id)
    {
        try{
            $assetMaintenance = AssetMaintenance::forCompany()->find($id);
            if(!$assetMaintenance)
            {
                return response()->json(['error' => true, 'message' => 'Asset Maintenance not found'],404);
            }
            $data = $request->validated();
            $assetMaintenance->updated_by = Auth::id();
            $assetMaintenance->update($data);
            return response()->json(['success' => true, 'message' => 'Asset Maintenance updated successfully'],200);
        }catch (\Exception $exception)
        {
            Log::error("Unable to update asset maintenance: " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to update asset maintenance'],400);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assetMaintenance = AssetMaintenance::forCompany()->find($id);
        if(!$assetMaintenance)
        {
            return response()->json(['error' => true, 'message' => 'Asset Maintenance not found'],404);
        }
        $assetMaintenance->delete();
        return response()->json(['success' => true, 'message' => 'Asset Maintenance deleted successfully'],200);
    }
}
