<?php

namespace App\Models;

use App\Helpers\DirectoryPathHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
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
      'image'
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

    public function assetMaintenances():HasMany
    {
        return $this->hasMany(AssetMaintenance::class,'asset_id');
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
    protected function imagePath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::assetImageDirectoryPath($this->assetCategory->company_id);


        if ($this->image && Storage::disk('public')->exists($imgPath . '/' . $this->image)) {
            $path = asset('storage/' . $imgPath . '/' . $this->image);
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

    public function scopeForCompany($query)
    {
        if (Auth::check()) {
            $company_id = Auth::user()->selectedCompany->company_id;
            $query->whereHas('assetCategory', function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }
        return $query;
    }
}
