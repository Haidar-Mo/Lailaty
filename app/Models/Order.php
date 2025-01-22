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
        'female_driver',
        'source_latitude',
        'source_longitude',
        'date',
        'time',
        'price',
        'type',
        'note',
        'status', //* 'pending' 'accepted' 'cancelled' 'ended'
        'number_of_seats',
        'auto_accept',
        'cancel_rason',
        'reference_key',
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

    public function destination(): HasMany
    {
        return $this->hasMany(OrderDestination::class);
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
