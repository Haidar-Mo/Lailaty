<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocuments extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'driver_license_frontFace'=>'nullable|image',
            'driver_license_backFace'=>'nullable|image',
            'personal_card_frontFace'=>'nullable|image',
            'personal_card_backFace'=>'nullable|image',
            'criminal_record'=>'nullable|image',
        ];
    }
}
