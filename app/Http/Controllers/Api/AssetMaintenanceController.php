<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetMaintenanceRequest;
use App\Http\Resources\AssetMaintenanceResource;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssetMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetMaintenance::with('createdBy','updatedBy','asset')->forCompany();

        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if ($value !== null && $value !== '') {
                    if (in_array($key, ['id', 'company_id', 'maintenance_status'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }

        $assetMaintenance = $query->latest()->paginate($request->page_size ?? 10);
        return AssetMaintenanceResource::collection($assetMaintenance);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssetMaintenanceRequest $request)
    {
        try {
            $data = $request->except('start_date','end_date','start_date_nepali','end_date_nepali');
            $start_date = $request->filled('start_date_nepali') ? DateHelper::nepaliToEnglish($request->start_date_nepali) : $request->start_date;
            $end_date = $request->filled('end_date_nepali') ? DateHelper::nepaliToEnglish($request->end_date_nepali) : $request->end_date;

            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $asset = Asset::find($request->asset_id);
            if($asset->status == 'sold' || $asset->status == 'disposed'){
                return response()->json(['error'=>true,'message'=>'Asset is already '. $asset->status."."],403);
            }
            $assetMaintenance = new AssetMaintenance();
            $assetMaintenance->fill($data);
            $assetMaintenance->created_by = Auth::id();
            $assetMaintenance->maintenance_status = false ;
            $assetMaintenance->save();
            $asset->update(['status'=>'maintenance']);
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
            $data = $request->except('start_date','end_date','start_date_nepali','end_date_nepali');
            $start_date = $request->filled('start_date_nepali') ? DateHelper::nepaliToEnglish($request->start_date_nepali) : $request->start_date;
            $end_date = $request->filled('end_date_nepali') ? DateHelper::nepaliToEnglish($request->end_date_nepali) : $request->end_date;

            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
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
     * Updates maintenance status
     * 
     */
    public function toggleMaintenanceStatus(string $id){
        try{
            $assetMaintenance = AssetMaintenance::forCompany()->find($id);
            if(!$assetMaintenance){
                return response()->json(['error' => true, 'message' => 'Asset Maintenance not found'],404);
            }
            $asset = Asset::find($assetMaintenance->asset_id);
            $asset->update(['status'=> !$assetMaintenance->maintenance_status == 0 ? 'maintenance' : 'used']);
            $assetMaintenance->update([
                'maintenance_status' => !$assetMaintenance->maintenance_status
            ]);

            return response()->json(['success' => true, 'message' => 'Asset Maintenance status changed successfully'],200);

        }catch(\Exception $ex){
            return response()->json(['error' => true, 'message' => 'Failed to update asset maintenance status'],200);
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
            $assetMaintenances = AssetMaintenance::forCompany()->whereIn('id', $ids);
            $count = $assetMaintenances->count();
            if ($count > 0) {
                $deleteStatus = $assetMaintenances->delete();

                return response()->json(['success' => true, 'message' => 'Asset maintenances deleted successfully.'], 200);
            }
            return response()->json(['error' => true, 'message' => 'Asset maintenances not found.'], 400);
        }catch (\Exception $exception){
            Log::error("Unable to delete asset maintenance " . $exception->getMessage());
            return response()->json(['error' => true, 'message' => 'Unable to delete asset maintenance'], 400);
        }
    }
}
