<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'latitude',
        'longitude',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function document(): HasOne
    {
        return $this->hasOne(OfficeDocument::class);
    }


    //!Accessories

    public function getDocumentsAttribute()
    {
        return $this->document()->first() ? $this->document()->first() : null;
    }
}
