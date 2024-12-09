<?php

namespace App\Models;

use App\Helpers\DirectoryPathHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
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
}
