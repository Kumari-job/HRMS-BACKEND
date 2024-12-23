<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Helpers\DirectoryPathHelper;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Traits\FileHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    use FileHelper;

    public function index(Request $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $query = Asset::forCompany();

        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if (in_array($key, ['id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }
        $assets = $query->latest()->paginate($request->page_size ?? 10);
        return AssetResource::collection($assets);
    }
    
    public function store(AssetRequest $request)
    {
        try{

            $company_id = Auth::user()->selectedCompany->company_id;
            if(Asset::where('code',$request->code)->forCompany()->exists()){
                return response()->json(['error'=>true, 'message'=>'Code has already been taken.'],403);
            }
            $data = $request->validated();
            $purchased_at = $request->filled('purchased_at_nepali') ? DateHelper::nepaliToEnglish($request->purchased_at_nepali) : $request->purchased_at;
            $data['purchased_at'] = $purchased_at;

            $warranty_end_at = $request->filled('warranty_end_at_nepali') ? DateHelper::nepaliToEnglish($request->warranty_end_at_nepali) : $request->warranty_end_at;
            $data['warranty_end_at'] = $warranty_end_at;

            $guarantee_end_at = $request->filled('guarantee_end_at_nepali') ? DateHelper::nepaliToEnglish($request->guarantee_end_at_nepali) : $request->guarantee_end_at;
            $data['guarantee_end_at'] = $guarantee_end_at;
            $asset = new Asset($data);

            if ($request->hasFile('warranty_image')) {
                $path = DirectoryPathHelper::warrantyImageDirectoryPath($company_id);
                $fileName = $this->fileUpload($request->file('warranty_image'), $path);
                $asset->warranty_image = $fileName;
            }
            if ($request->hasFile('guarantee_image')) {
                $path = DirectoryPathHelper::guaranteeImageDirectoryPath($company_id);
                $fileName = $this->fileUpload($request->file('guarantee_image'), $path);
                $asset->guarantee_image = $fileName;
            }
            if ($request->hasFile('image'))
            {
                $path = DirectoryPathHelper::assetImageDirectoryPath($company_id);
                $fileName = $this->fileUpload($request->file('image'), $path);
                $asset->image = $fileName;
            }
            $asset->created_by = Auth::id();
            $asset->save();
            return response()->json(['success'=>true,'message'=>'Asset created successfully'],201);
        }catch (Exception $e)
        {
            Log::error('Unable to create asset: '. $e->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to create asset'],400);
        }
    }

    public function show($id)
    {

        $asset = Asset::with('assetCategory','vendor','assetMaintenances','assetUsages')->forCompany()->find($id);
        if(!$asset)
        {
            return response()->json(['error'=>true,'message'=>'Asset not found'],404);
        }
        return new AssetResource($asset);
    }

    public function update(AssetRequest $request, $id)
    {
        try {
            $company_id = Auth::user()->selectedCompany->company_id;

            if(Asset::where('code',$request->code)->forCompany()->where('id','!=',$id)->exists()){
                return response()->json(['error'=>true, 'message'=>'Code has already been taken.'],403);
            }
            $asset = Asset::with('assetCategory', 'vendor')->forCompany()->find($id);

            if (!$asset) {
                return response()->json(['error' => true, 'message' => 'Asset not found'], 404);
            }
            $data = $request->except('warranty_image', 'guarantee_image','purchased_at','warranty_end_at','guarantee_end_at');

            $purchased_at = $request->filled('purchased_at_nepali') ? DateHelper::nepaliToEnglish($request->purchased_at_nepali) : $request->purchased_at;
            $data['purchased_at'] = $purchased_at;

            $warranty_end_at = $request->filled('warranty_end_at_nepali') ? DateHelper::nepaliToEnglish($request->warranty_end_at_nepali) : $request->warranty_end_at;
            $data['warranty_end_at'] = $warranty_end_at;

            $guarantee_end_at = $request->filled('guarantee_end_at_nepali') ? DateHelper::nepaliToEnglish($request->guarantee_end_at_nepali) : $request->guarantee_end_at;
            $data['guarantee_end_at'] = $guarantee_end_at;

            if ($request->hasFile('warranty_image')) {
                $path = DirectoryPathHelper::warrantyImageDirectoryPath($company_id);
                if ($asset->warranty_image) {
                    $this->fileDelete($path, $asset->warranty_image);
                }
                $fileName = $this->fileUpload($request->file('warranty_image'), $path);
                $data['warranty_image'] = $fileName;
            }
            if ($request->hasFile('guarantee_image')) {
                $path = DirectoryPathHelper::guaranteeImageDirectoryPath($company_id);
                if ($asset->guarantee_image) {
                    $this->fileDelete($path, $asset->guarantee_image);
                }
                $fileName = $this->fileUpload($request->file('guarantee_image'), $path);
                $data['guarantee_image'] = $fileName;
            }
            if ($request->hasFile('image')) {
                $path = DirectoryPathHelper::assetImageDirectoryPath($company_id);
                if ($asset->image) {
                    $this->fileDelete($path, $asset->image);
                }
                $fileName = $this->fileUpload($request->file('image'), $path);
                $data['image'] = $fileName;
            }
            $asset->updated_by = Auth::id();
            $asset->update($data);
            return response()->json(['success' => true, 'message' => 'Asset updated successfully'], 200);
        } catch (Exception $e)
        {
            Log::error('Unable to update asset: '. $e->getMessage());
        }
    }
    public function destroy(Request $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $validator = Validator::make($request->all(), [
            'ids' => 'array'
        ]);
        $ids = $request->ids;
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors(), 'message' => MessageHelper::getErrorMessage('form')], 422);
        }
        $assets = Asset::whereIn('id', $ids)->forCompany();
        $count = $assets->count();
        if ($count > 0) {
            foreach ($assets as $asset) {
                if ($asset->warranty_image)
                {
                    $path = DirectoryPathHelper::warrantyImageDirectoryPath($company_id);
                    $this->fileDelete($path, $asset->warranty_image);
                }
                if ($asset->guarantee_image)
                {
                    $path = DirectoryPathHelper::guaranteeImageDirectoryPath($company_id);
                    $this->fileDelete($path, $asset->guarantee_image);
                }
                if ($asset->image)
                {
                    $path = DirectoryPathHelper::assetImageDirectoryPath($company_id);
                    $this->fileDelete($path, $asset->image);
                }

            }
            $deleteStatus = $assets->delete();

            return response()->json(['success' => true, 'message' => 'Assets deleted successfully.'], 200);
        }
        return response()->json(['error' => true, 'message' => 'Assets not found.'], 400);
    }
}
