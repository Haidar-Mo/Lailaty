<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions;


    protected $fillable = [
        'email',
        'password',
        'phone_number',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'deviceToken',
        'is_active',
        'rate',
        'office_id',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
    ];

    protected $appends = [
        'image_url',
        'rate',
        'is_full_registered',
        'role_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
        'email_verified_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'verification_code_expires_at' => 'datetime',
        'password' => 'hashed',

    ];



    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function registrationDocument(): HasOne
    {
        return $this->hasOne(RegistrationDocument::class);
    }

    public function office(): HasOne
    {
        return $this->hasOne(Office::class);
    }

    public function vehicle(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function driveVehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class, 'driver_id');
    }

    public function workRequest(): HasMany
    {
        return $this->hasMany(VehicleWorkRequest::class);
    }

    public function receivedWorkRequest(): HasMany
    {
        return $this->hasMany(VehicleWorkRequest::class, 'receiver_user_id');
    }
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function offer(): HasMany
    {
        return $this->hasMany(OrderOffer::class);
    }

    public function report(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function rate(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function rated(): MorphMany
    {
        return $this->morphMany(Rate::class, 'rateable');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }


    /** Accessories */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image->path);
        }
        return null;
    }

    public function getIsFullRegisteredAttribute()
    {
        if ($this->first_name && $this->last_name && $this->gender && $this->birth_date) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getRoleNameAttribute()
    {
        return $this->roles()->first()->name;
    }
    public function getRateAttribute()
    {
        $count = $this->rated()->count();
        return $count > 0 ? $this->rate()->sum('rate') / $count : 0;
    }
}
