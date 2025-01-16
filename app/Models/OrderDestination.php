<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDestination extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'source_latitude',
        'source_longitude',
        'destination_latitude',
        'destination_longitude',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
