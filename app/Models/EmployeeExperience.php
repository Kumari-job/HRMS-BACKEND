<?php

namespace App\Models;

use App\Helpers\DirectoryPathHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeExperience extends Model
{
    use LogsActivity;
    protected $table = 'employee_experiences';

    protected $fillable = [
        'employee_id',
        'designation',
        'industry',
        'job_level',
        'company',
        'experience_letter',
        'from_date',
        'to_date',
        'created_by',
        'updated_by',
    ];

    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "An employee experience has been {$eventName}")
            ->logOnly(['employee.name','employee_id']);
    }
    protected function experienceLetterPath(): Attribute
    {
        $defaultPath = asset('assets/images/image.jpg');
        $imgPath = DirectoryPathHelper::experienceDirectoryPath($this->employee->company_id, $this->employee_id);

        if ($this->experience_letter && Storage::disk('public')->exists($imgPath . '/' . $this->experience_letter)) {
            $path = asset('storage/' . $imgPath . '/' . $this->experience_letter);
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
