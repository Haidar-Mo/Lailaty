<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_card',
        'commercial_registration_card',
        'insurance_card',
        'value_added_tax_card',
        'attached_document',
    ];


    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}
