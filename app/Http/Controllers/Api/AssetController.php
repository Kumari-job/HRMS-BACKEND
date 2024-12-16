<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DirectoryPathHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Traits\FileHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    use FileHelper;

    public function index()
    {
        $company_id = Auth::user()->selectedCompany()->company_id;
    }
    
    public function store(AssetRequest $request)
    {
        try{
            $company_id = Auth::user()->selectedCompany()->company_id;
        $asset = new Asset($request->all());
        
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
        $asset->save();
        return response()->json(['success'=>true,'message'=>'Asset created successfully'],201);
        }catch (Exception $e)
        {
            Log::error('Unable to create asset: '. $e->getMessage());
            return response()->json(['error'=>true,'message'=>'Unable to create asset'],400);
        }
        
    }
}
