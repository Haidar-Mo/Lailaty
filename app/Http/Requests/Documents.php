<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Documents extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'personal_image' => 'required|image',
            'driver_license_frontFace' => 'required|image',
            'driver_license_backFace' => 'required|image',
            'personal_card_frontFace' => 'required|image',
            'personal_card_backFace' => 'required|image',
            'criminal_record' => 'nullable|image',
            'birth_date' => 'required|date'
        ];
    }
}
