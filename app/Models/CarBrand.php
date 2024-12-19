<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarBrand extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
    ];


    public function car(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
