<?php

namespace App\Models;

use App\Helpers\DirectoryPathHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EmployeeDocument extends Model
{
    protected $table = 'employee_documents';
    protected $fillable = [
        'employee_id',
        'citizenship_front',
        'citizenship_back',
        'driving_license',
        'passport',
        'pan_card',
        'created_by',
        'updated_by',
    ];

    protected function citizenshipFrontPath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::citizenshipFrontDirectoryPath($this->employee->company_id, $this->employee->id);

        if ($this->citizenship_front && Storage::disk('public')->exists($imgPath . '/' . $this->citizenship_front)) {
            $path = asset('storage/' . $imgPath . '/' . $this->citizenship_front);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }

    protected function citizenshipBackPath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::citizenshipBackDirectoryPath($this->employee->company_id,$this->employee->id);


        if ($this->citizenship_back && Storage::disk('public')->exists($imgPath . '/' . $this->citizenship_back)) {
            $path = asset('storage/' . $imgPath . '/' . $this->citizenship_back);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }

    protected function drivingLicensePath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::drivingLicenseDirectoryPath($this->employee->company_id, $this->employee->id);


        if ($this->driving_license && Storage::disk('public')->exists($imgPath . '/' . $this->driving_license)) {
            $path = asset('storage/' . $imgPath . '/' . $this->driving_license);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }

    protected function passportPath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::passportDirectoryPath($this->employee->company_id, $this->employee->id);


        if ($this->passport && Storage::disk('public')->exists($imgPath . '/' . $this->passport)) {
            $path = asset('storage/' . $imgPath . '/' . $this->passport);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }

    protected function panCardPath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::panCardDirectoryPath($this->employee->company_id ,$this->employee->id);


        if ($this->pan_card && Storage::disk('public')->exists($imgPath . '/' . $this->pan_card)) {
            $path = asset('storage/' . $imgPath . '/' . $this->pan_card);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id',);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
