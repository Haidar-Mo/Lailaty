<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleService;
use App\Services\Mobile\DriverServiceRegisterService;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Exception;

class VehicleServiceRegisterController extends Controller
{

    use Responses;

    public function __construct(protected DriverServiceRegisterService $service)
    {
    }

    public function index()
    {
        return VehicleService::all();
    }


    /**
     * choose a transport service for a vehicle.
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(string $id, Request $request)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            if ($vehicle->vehicle_type === 'motorcycle')
                $vehicle = $this->service->updateServiceForMotorcycle($vehicle);
            else {
                $data = $request->validate([
                    'wedding' => 'boolean',
                    'wedding_category_id' => 'nullable',
                    'taxi' => 'boolean',
                    'travel' => 'boolean',
                    'drive_lessons' => 'boolean',
                    'shipping' => 'boolean',
                ]);
                $vehicle = $this->service->updateServiceForCar($vehicle, $data);
            }
            return $this->indexOrShowResponse('vehicle', $vehicle, 201);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }

    }


    public function show(string $id)
    {
        //
    }


    /**
     * Update an existing transport service for a vehicle.
     * 
     * @param string $id The ID of the vehicle to update.
     * @param \Illuminate\Http\Request $request The request containing updated service data.
     * 
     * @return \Illuminate\Http\JsonResponse The updated vehicle data or an error response.
     */
    public function update(string $id, Request $request)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            if ($vehicle->vehicle_type === 'motorcycle') {
                $vehicle = $this->service->updateServiceForMotorcycle($vehicle);
            } else {
                $data = $request->validate([
                    'wedding' => 'boolean',
                    'wedding_category_id' => 'nullable',
                    'taxi' => 'boolean',
                    'travel' => 'boolean',
                    'drive_lessons' => 'boolean',
                    'shipping' => 'boolean',
                ]);
                $vehicle = $this->service->updateServiceForCar($vehicle, $data);
            }
            return $this->indexOrShowResponse('vehicle', $vehicle, 200);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }


    public function destroy(string $id)
    {
        //
    }
}
