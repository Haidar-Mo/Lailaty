<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'tax_card',
        'commercial_registration_card',
        'insurance_card',
        'value_added_tax_card',
        'attached_document',
    ];

    protected $append = [
        'tax_card_full_path',
        'commercial_registration_card_full_path',
        'insurance_card_full_path',
        'value_added_tax_card_full_path',
        'attached_document_full_path',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }



    //! Accessories 

    public function getTaxCardFullPathAttribute()
    {
        return $this->generateFullPath($this->tax_card);
    }

    public function getCommercialRegistrationCardFullPathAttribute()
    {
        return $this->generateFullPath($this->commercial_registration_card);
    }

    public function getInsuranceCardFullPathAttribute()
    {
        return $this->generateFullPath($this->insurance_card);
    }

    public function getValueAddedTaxCardFullPathAttribute()
    {
        return $this->generateFullPath($this->value_added_tax_card);
    }

    public function getAttachedDocumentFullPathAttribute()
    {
        return $this->generateFullPath($this->attached_document);
    }

    private function generateFullPath($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}
