<?php

namespace App\Http\Resources;

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
            'description' => $this->description,
            'brand' => $this->brand,
            'cost' => $this->cost,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'purchased_at' => Carbon::parse($this->purchased_at)->format('Y-m-d'),
            'purchased_at_formatted' => Carbon::parse($this->purchased_at)->format('d M Y'),
            'warranty_end_at' => Carbon::parse($this->warranty_end_at)->format('Y-m-d'),
            'warranty_end_at_formatted' => Carbon::parse($this->warranty_end_at)->format('d M Y'),
            'warranty_image' => $this->warranty_image,
            'warranty_image_path' => $this->warranty_image_path,
            'guarantee_image' => $this->guarantee_image,
            'guarantee_image_path' => $this->guarantee_image_path,
            'status' => $this->status,
            'asset_category' => new AssetCategoryResource($this->whenLoaded('assetCategory')),
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
        ];
    }
}
