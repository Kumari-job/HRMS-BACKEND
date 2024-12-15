<?php

namespace App\Models;

use App\Helpers\DirectoryPathHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'image',
        'mobile',
        'address',
        'gender',
        'date_of_birth',
        'marital_status',
        'blood_group',
        'religion',
    ];

    protected function imagePath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::employeeImageDirectoryPath($this->company_id);


        if ($this->image && Storage::disk('public')->exists($imgPath . '/' . $this->image)) {
            $path = asset('storage/' . $imgPath . '/' . $this->image);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }

    public function employeeAddress(): HasOne
    {
        return $this->hasOne(EmployeeAddress::class);
    }

    public function employeeBenefit(): HasOne
    {
        return $this->hasOne(EmployeeBenefit::class);
    }

    public function employeeContracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function employeeDocument(): HasOne
    {
        return $this->hasOne(EmployeeDocument::class);
    }

    public function employeeEducations(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function employeeExperiences(): HasMany
    {
        return $this->hasMany(EmployeeExperience::class);
    }

    public function employeeFamilies(): HasMany
    {
        return $this->hasMany(EmployeeFamily::class);
    }

    public function employeeOnboardings(): HasMany
    {
        return $this->hasMany(EmployeeOnboarding::class);
    }

    public function employeeBanks(): HasMany
    {
        return $this->hasMany(EmployeeBank::class);
    }

    public function getTotalExperienceAttribute()
    {
        $totalMonths = $this->employeeExperiences->reduce(function ($carry, $experience) {
            $fromDate = Carbon::parse($experience->from_date);
            $toDate = $experience->to_date ? Carbon::parse($experience->to_date) : Carbon::now();

            return $carry + $fromDate->diffInMonths($toDate);
        }, 0);

        return round($totalMonths / 12);
    }
}
