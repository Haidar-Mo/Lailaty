<?php

namespace App\Http\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;

class VehicleCreateRequest extends FormRequest
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
            $rules = match ($vehicleType) {
                'car' => [
                    'vehicle_type' => $vehicleType,
                    'license_plate' => 'required|string',
                    'model_year' => 'required',
                    'car_brand_id' => 'required|string',
                    'color' => 'nullable',
                    'gear_type' => 'required|string|in:manual,auto',
                    'is_modified' => 'required',
                    'original_car_brand_id' => 'nullable',
                    'more_the_four_seats' => 'boolean',
                    'wedding_category_id' => 'string',
                    'image_1' => 'required|image',
                    'image_2' => 'required|image',
                    'image_3' => 'required|image',
                    'image_4' => 'required|image',
                    'image_5' => 'required|image',
                    'face_1' => 'required|image',
                    'face_2' => 'required|image'
                ],
                'motorcycle' => [
                    'vehicle_type' => $vehicleType,
                    'model_year' => 'required',
                    'license_plate' => 'required|string',
                    'color' => 'nullable',
                    'image_1' => 'required|image',
                    'face_1' => 'required|image',
                    'face_2' => 'required|image'
                ],
                default => throw new Exception('Invalid vehicle type'),
            };
    }
}
