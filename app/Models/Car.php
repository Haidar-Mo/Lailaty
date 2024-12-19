<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'owber_id',
        'driver_id',
        'wedding_category_id',
        'car_brand_id',
        'gear_type',
        'is_modified',
        'original_car_brand_id',
        'available',
        'number_of_seats',
        'rate',
        'latitude',
        'longitude',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dreiver_id');
    }

    public function drivingRequest(): HasMany
    {
        return $this->hasMany(UserCarDrivingRequest::class);
    }

    public function carBrand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function weddingCategory(): BelongsTo
    {
        return $this->belongsTo(WeddingCategory::class);
    }

    public function image(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function car_service(): HasMany
    {
        return $this->hasMany(CarService::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function offer(): HasMany
    {
        return $this->hasMany(OrderOffer::class);
    }

    public function rate(): HasMany
    {
        return $this->hasMany(Rate::class);
    }
}
