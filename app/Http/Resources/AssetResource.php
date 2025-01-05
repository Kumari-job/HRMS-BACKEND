<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use App\Models\AssetDispose;
use App\Models\AssetSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_category_id' => $this->asset_category_id,
            'vendor_id' => $this->vendor_id,
            'code' => $this->code,
            'title' => $this->title,
            'image_path' => $this->image_path,
            'image' => $this->image,
            'description' => $this->description,
            'brand' => $this->brand,
            'cost' => $this->cost,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'purchased_at' => Carbon::parse($this->purchased_at)->format('Y-m-d'),
            'purchased_at_formatted' => Carbon::parse($this->purchased_at)->format('d M Y'),
            'warranty_end_at' => $this->warranty_end_at ? Carbon::parse($this->warranty_end_at)->format('Y-m-d') : null,
            'warranty_end_at_formatted' => $this->warranty_end_at ? Carbon::parse($this->warranty_end_at)->format('d M Y') : null,
            'warranty_end_at_nepali_formatted' => $this->warranty_end_at ? DateHelper::englishToNepali($this->warranty_end_at) : null,
            'warranty_end_at_nepali' => $this->warranty_end_at ? DateHelper::englishToNepali($this->warranty_end_at, 'Y-m-d') : null,
            'warranty_image' => $this->warranty_image,
            'warranty_image_path' => $this->warranty_image_path,
            'guarantee_end_at' => $this->guarantee_end_at ? Carbon::parse($this->guarantee_end_at)->format('Y-m-d') : null,
            'guarantee_end_at_formatted' => $this->guarantee_end_at ? Carbon::parse($this->guarantee_end_at)->format('d M Y') : null,
            'guarantee_end_at_nepali_formatted' => $this->guarantee_end_at ? DateHelper::englishToNepali($this->guarantee_end_at) : null,
            'guarantee_end_at_nepali' => $this->guarantee_end_at ? DateHelper::englishToNepali($this->guarantee_end_at, 'Y-m-d') : null,
            'guarantee_image' => $this->guarantee_image,
            'guarantee_image_path' => $this->guarantee_image_path,
            'status' => $this->status,
            'asset_category' => new AssetCategoryResource($this->whenLoaded('assetCategory')),
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'asset_usages' => AssetUsageResource::collection($this->whenLoaded('assetUsages')),
            'asset_under_maintenance' => new AssetMaintenanceResource($this->whenLoaded('assetUnderMaintenance')),
            'asset_dispose' => AssetDisposeResource::collection($this->whenLoaded('assetDispose')),
            'asset_sale' => AssetSaleResource::collection($this->whenLoaded('assetSale')),
        ];
    }
}
