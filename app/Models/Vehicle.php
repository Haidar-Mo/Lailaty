<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'vehicle_type',
        'license_plate',
        'model_year',
        'car_brand_id',
        'is_modified',
        'original_car_brand_id',
        'color',
        'gear_type',
        'available',
        'more_than_four_seats',
        'is_comfort',
        'wedding_category_id',
        'rate',
        'latitude',
        'longitude',
    ];


    protected $appends = [
        'car_brand',
        'original_car_brand',
        'wedding_category',
        'image',
        'ownership_document',
        'order_count',
        'rate',
    ];



    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dreiver_id');
    }

    public function workRequest(): HasMany
    {
        return $this->hasMany(VehicleWorkRequest::class);
    }

    public function carBrand(): BelongsTo
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function originalCarBrand(): BelongsTo
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

    public function ownershipDocument(): HasMany
    {
        return $this->hasMany(VehicleOwnershipDocument::class);
    }

    public function service(): HasMany
    {
        return $this->hasMany(VehicleService::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function offer(): HasMany
    {
        return $this->hasMany(OrderOffer::class);
    }

    public function rated(): MorphMany
    {
        return $this->morphMany(Rate::class, 'rateable');
    }



    /** Accessories */

    /**
     * Check if the vehicle have the luxury service or not
     * @return bool
     */
    public function isComfortable()
    {
        $luxury = Service::where('name', 'luxury')->first();
        return $this->service()->where('service_id', $luxury->id)->first() ? true : false;
    }


    
    /** Attributes */

    public function getCarBrandAttribute()
    {
        if ($this->carBrand()->first() != null)
            return $this->carBrand()->first()->name;
        return null;
    }

    public function getWeddingCategoryAttribute()
    {
        return $this->weddingCategory()->first();
    }

    public function getOriginalCarBrandAttribute()
    {
        return $this->originalCarBrand()->first();
    }

    public function getImageAttribute()
    {
        return $this->image()->get();
    }

    public function getOwnershipDocumentAttribute()
    {
        return $this->ownershipDocument()->first();
    }

    public function getOrderCountAttribute()
    {
        $count = $this->order()->where('status', 'ended')->get()->count();
        return $count > 0 ? $count : 0;
    }
    public function getRateAttribute()
    {
        $count = $this->rated()->count();
        return $count > 0 ? $this->rate()->sum('rate') / $count : 0;
    }

}
