<?php

namespace App\Models;

use App\Helpers\DirectoryPathHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Asset extends Model
{
    public $table = 'assets';

    public $fillable = [
      'asset_category_id',
      'vendor_id',
      'code',
      'title',
      'description',
      'brand',
      'cost',
      'model',
      'serial_number',
      'purchased_at',
      'warranty_end_at',
        'warranty_image',
        'guarantee_end_at',
        'guarantee_image',
        'status',
    ];

    public function assetCategory():BelongsTo
    {
        return $this->belongsTo(AssetCategory::class,'asset_category_id');
    }
    public function vendor():BelongsTo
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }

    public function assetDispose():HasOne
    {
        return $this->hasOne(AssetDispose::class,'asset_id');
    }
    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy():BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }
    protected function warrantyImagePath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::warrantyImageDirectoryPath($this->assetCategory->company_id);


        if ($this->warranty_image && Storage::disk('public')->exists($imgPath . '/' . $this->warranty_image)) {
            $path = asset('storage/' . $imgPath . '/' . $this->warranty_image);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }
    protected function guaranteeImagePath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::warrantyImageDirectoryPath($this->assetCategory->company_id);


        if ($this->guarantee_image && Storage::disk('public')->exists($imgPath . '/' . $this->guarantee_image)) {
            $path = asset('storage/' . $imgPath . '/' . $this->guarantee_image);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }
}
