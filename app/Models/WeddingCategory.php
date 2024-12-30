<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeddingCategory extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'min_price',
    ];


    
    public function vehicle(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

}
