<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\Mobile\DriverVehicleService;
use App\Traits\Responses;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Enums\VehicleType;
class VehicleController extends Controller
{
    use Responses;


    public function __construct(protected DriverVehicleService $driverVehicleService)
    {

    }

    public function index()
    {
        return response()->json(Auth::user()->vehicle());
    }

    public function show($id)
    {

    }
    public function store(Request $request, VehicleType $vehicleType)
    {

        $rules = match ($vehicleType) {
            'car' => [
                'vehicle_type' => $vehicleType,
                'wedding_category_id' => 'required|string',
                'car_brand_id' => 'required|string',
                'gear_type' => 'required|string|in:manual,auto',
                'is_modified' => 'required',
                'original_car_brand_id' => 'nullable',
                'latitude' => 'required',
                'longitude' => 'required',
                'side_face_image_1' => 'required|image',
                'side_face_image_2' => 'required|image',
                'front_face_image' => 'required|image',
                'back_face_image' => 'required|image',
                'inside_image' => 'required|image',
            ],
            'motorcycle' => [
                'vehicle_type' => $vehicleType,
                'model_year' => 'required',
                'image' => 'required|image'
            ],
            default => throw new Exception('Invalid vehicle type'),
        };

        $request->validate($rules);

        DB::beginTransaction();
        try {

            $vehicle = $this->driverVehicleService->addNewVehicle($request, $request->user());
            $vehicle->load('image');
            DB::commit();
            return $this->indexOrShowResponse('vehicle', $vehicle, 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sudResponse($e->getMessage(), 500);
        }
    }

    public function upadate()
    {
    }

    public function destroy()
    {
    }

    public function showAllFleetOwner()
    {
    }

    public function showAvailableCar()
    {
    }

    public function requestToDriveCar()
    {
    }

    public function respondeToDriveCarRequest()
    {
    }

    public function deleteMyDrivecarRequest()
    {
    }
}
