<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'user_id',
        'service_id',
        'price',
        'number_of_seats',
        'female_driver',
        'note',
        'type',
        'status',
        'cancel_rason'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function location(): HasMany
    {
        return $this->hanMany(OrderLocation::class);
    }

    public function duration(): HasOne
    {
        return $this->hasOne(OrderDuration::class);
    }

    public function offer(): HasMany
    {
        return $this->hasMany(OrderOffer::class);
    }

}
