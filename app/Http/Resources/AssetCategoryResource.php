<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetCategoryResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'created_at_formatted' => Carbon::parse($this->created_at)->format('d M Y'),
            'updated_at_formatted' => Carbon::parse($this->updated_at)->format('d M Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
        ];
    }
}
