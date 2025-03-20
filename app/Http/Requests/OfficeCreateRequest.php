<?php

namespace App\Http\Requests;

use App\Rules\EgyptianPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class OfficeCreateRequest extends FormRequest
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
        $type = $this->route('type');

        return match ($type) {
            'personal' => [
                'name' => 'required|string|max:255',
                'phone_number' => ['required', 'string', 'unique:offices,phone_number'],
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ],
            'company' => [
                'name' => 'required|string|max:255',
                'phone_number' => ['required', 'string', 'unique:offices,phone_number'],
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'tax_card' => 'required|image',
                'commercial_registration_card' => 'required|image',
                'insurance_card' => 'sometimes|image',
                'value_added_tax_card' => 'sometimes|image',
                'attached_document' => 'nullable|image',
            ]
        };
    }
}
