<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationDocument extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'personal_image',
        'driver_license_frontFace',
        'driver_license_backFace',
        'personal_card_frontFace',
        'personal_card_backFace',
        'criminal_record',
        'birth_date',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
