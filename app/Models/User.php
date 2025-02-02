<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'idp_user_id',
        'name',
        'email',
        'mobile',
        'image_path',
        'is_password_changed',
    ];

    public function getImagePathAttribute($value)
    {
        return $value ?? asset('assets/images/image.jpg');
    }

    protected static function booted()
    {
        static::updated(function ($user) {
            if ($user->isDirty(['email', 'name'])) {
                $user->employee->update([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
            }
        });
    }

    public function selectedCompany(): HasOne
    {
        return $this->hasOne(SelectedCompany::class);
    }

    public function employee():BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function attendances():HasMany
    {
        return $this->hasMany(Attendance::class,'employee_id');
    }
}
