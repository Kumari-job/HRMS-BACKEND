<?php

namespace App\Models;

use App\Helpers\DirectoryPathHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeEducation extends Model
{
    use LogsActivity;
    protected $table = 'employee_education';

    protected $fillable = [
        'employee_id',
        'degree',
        'field_of_study',
        'institution',
        'university_board',
        'certificate',
        'from_date',
        'to_date',
    ];
    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "An employee education has been {$eventName}")
            ->logOnly(['employee.name','employee_id']);
    }
    protected function certificatePath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::educationDirectoryPath($this->employee->company_id, $this->employee_id);

        if ($this->certificate && Storage::disk('public')->exists($imgPath . '/' . $this->certificate)) {
            $path = asset('storage/' . $imgPath . '/' . $this->certificate);
        } else {
            $path = $defaultPath;
        }

        return Attribute::make(
            get: fn () => $path
        );
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy():BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
