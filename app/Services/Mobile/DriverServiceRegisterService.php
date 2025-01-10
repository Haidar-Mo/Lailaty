<?php

namespace App\Services\Mobile;

use App\Models\Service;
use App\Models\VehicleService;
use DB;
use Exception;


class DriverServiceRegisterService
{


    public function registerServiceForMotorcycle($vehicle)
    {
        DB::beginTransaction();
        try {
            $service = Service::where('name', 'داخلي')->firstOrFail();
            $vehicle->service->create([
                'service_id' => $service->id,
                'is_activated' => true
            ]);

            //!  subscribe to firebse channel 

            DB::commit();
            $vehicle->load('service');
            return $vehicle;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), 500);
        }
    }


    public function registerService($vehicle, $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data as $key => $value) {

                $service = Service::where('name', $key)->first();
                $registerd_service = new VehicleService();
                $registerd_service->vehicle_id = $vehicle->id;
                $registerd_service->service_id = $service->id;
                $registerd_service->is_activated = $value;
                if ($key === 'زفاف') {
                    $registerd_service->price = $data->price;
                }
                $registerd_service->save();
            }

            //!  subscribe to firebse channel

            DB::commit();
            $vehicle->load('service');
            return $vehicle;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), 500);
        }
    }
}
