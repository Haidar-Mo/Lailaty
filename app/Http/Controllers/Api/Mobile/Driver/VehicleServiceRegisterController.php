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


    public function store(string $id, Request $request)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            if ($vehicle->vehicleType === 'motorcyle')
                $vehicle = $this->service->registerServiceForMotorcycle($vehicle);
            else {
                $data = $request->validate([
                    'زفاف' => 'boolean',
                    'داخلي' => 'boolean',
                    'سفر' => 'boolean',
                    'price' => 'nullable',
                    'تعليم قيادة' => 'boolean',
                    'شحن' => 'boolean',
                ]);
                $vehicle = $this->service->registerService($vehicle, $data);
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


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
