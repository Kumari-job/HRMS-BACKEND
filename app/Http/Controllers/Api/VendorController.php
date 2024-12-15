<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->selectedComany->company_id;

        $query = Vendor::where('company_id', $company_id);

        if (!empty($request->except('page', 'page_size'))) {
            foreach ($request->except('page', 'page_size') as $key => $value) {
                if (isset($value) && !empty($value)) {
                    if (in_array($key, ['id', 'company_id'])) {
                        $query->where($key, $value);
                    } else {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    }
                }
            }
        }
        $vendors = $query->latest()->paginate($request->page_size ?? 10);

    }

    public function store(VendorRequest $request)
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $vendor = new Vendor($request->validated());
        $vendor->created_by = Auth::id();
        $vendor->company_id = $company_id;
        $vendor->save();
        return response()->json(['success' => true,'message'=>'Vendor created successfully'],201);
    }
}
