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
            'personal_image' => 'sometimes|image',
            'driver_license_frontFace' => 'sometimes|image',
            'driver_license_backFace' => 'sometimes|image',
            'personal_card_frontFace' => 'sometimes|image',
            'personal_card_backFace' => 'sometimes|image',
            'criminal_record' => 'sometimes|image',
            'birth_date' => 'sometimes|date'
        ];
    }
}
