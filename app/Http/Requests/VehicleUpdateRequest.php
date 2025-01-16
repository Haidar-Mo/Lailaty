<?php

namespace App\Http\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;

class VehicleUpdateRequest extends FormRequest
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
        $vehicleType = $this->route('vehicleType');

        return
            match ($vehicleType) {
                'car' => [
                    'wedding_category_id' => 'string',
                    'car_brand_id' => 'string',
                    'gear_type' => 'string|in:manual,auto',
                    'license_plate'=>'required|string',
                    'color'=>'nullable',
                    'is_modified' => 'boolean',
                    'original_car_brand_id' => 'nullable',
                    'more_the_four_seats' => 'boolean'
                ],
                'motorcycle' => [
                    'model_year' => 'required',
                ],
                default => throw new Exception('Invalid vehicle type'),
            };
    }
}