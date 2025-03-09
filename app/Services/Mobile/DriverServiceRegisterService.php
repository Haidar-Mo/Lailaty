<?php

namespace App\Services\Mobile;

use App\Models\Service;
use DB;
use Exception;


class DriverServiceRegisterService
{

    public function updateServiceForMotorcycle($vehicle)
    {
        DB::beginTransaction();
        try {
            $service = Service::where('name', 'داخلي')->firstOrFail();
            $vehicle->service()->updateOrCreate(
                ['service_id' => $service->id],
                [
                    'is_activated' => true
                ]
            );

            //DO:  subscribe to firebase channel 

            DB::commit();
            $vehicle->load('service');
            return $vehicle;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), 500);
        }
    }


    public function updateServiceForCar($vehicle, $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data as $key => $value) {
                $service = Service::where('name', $key)->first();
                if ($service) {
                    $vehicleService = $vehicle->service()->where('service_id', $service->id)->first();
                    if ($vehicleService) {
                        $vehicleService->update(['is_activated' => $value]);
                    } else {
                        $vehicle->service()->create(['service_id' => $service->id, 'is_activated' => $value]);
                    }
                }

                if ($key === 'wedding' && $value == true) {
                    $vehicle->wedding_category_id = $data['wedding_category_id'] ?? null;
                    $vehicle->save();
                }
            }

            //DO: update subscription to firebase channel if needed

            DB::commit();
            $vehicle->load('service');
            return $vehicle;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), 500);
        }
    }


}
