<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'source',
        'destination',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
