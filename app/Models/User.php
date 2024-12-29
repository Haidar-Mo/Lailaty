<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
        'deviceToken',
        'is_active',
        'full_registered',
       // 'rate',
        'office_id',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
    ];

    protected $appends = [
        'image_url',
        'rate',
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

    public function workIn(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
    public function office(): HasOne
    {
        return $this->hasOne(Office::class);
    }
    public function car(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function drivingRequest(): HasMany
    {
        return $this->hasMany(UserCarDrivingRequest::class);
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

    public function rated(): MorphToMany
    {
        return $this->morphToMany(Rate::class, 'rateable');
    }



    /** Accessories */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image->path);
        }
        return null;
    }

    public function getRateAttribute():float
    {
        if ($this->getRoleNames()[0] === 'client') {
            $ratings = $this->rated;
            if ($ratings->isNotEmpty()) {
                $total = $ratings->sum('rate');
                return round($total / $ratings->count(), 1);
            }
        }
        return 0;
    }



}
