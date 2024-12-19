<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];


    public function carService(): HasMany
    {
        return $this->hasMany(CarService::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
