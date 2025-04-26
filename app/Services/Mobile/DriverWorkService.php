<?php

namespace App\Services\Mobile;

use App\Models\Vehicle;
use App\Models\VehicleWorkRequest;
use App\Traits\Responses;
use DB;
use Exception;
use Illuminate\Foundation\Auth\User;


class DriverWorkService
{

    use Responses;
    public function createWorkRequest(Vehicle $vehicle, User $user)
    {
        DB::beginTransaction();
        try {
            $old_request = $user->workRequest()->where('vehicle_id', $vehicle->id)->Where('status', 'pending')->first();
            if ($old_request) {
                DB::rollBack();
                throw new Exception('you already send request to this vehicle', 422);
            }

            $request = $vehicle->workRequest()->create([
                'user_id' => $user->id,
                'receiver_user_id' => $vehicle->user_id,
                'status' => 'pending'
            ]);

            //! send notification to fleet-Owner

            DB::commit();
            return $request;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage(), 500);
        }
    }


    public function approveWorkRequest(VehicleWorkRequest $request ,Vehicle $vehicle, User $user)
    {

        DB::beginTransaction();
        try {

            if ($vehicle->driver()) {
                $request->delete();
                DB::commit();
                return $this->sudResponse('This vehicle is already has a driver \\n request has been deleted...', 200);
            }
            if ($user->driveVehicle()) {
                $request->delete();
                DB::commit();
                return $this->sudResponse('This driver is already driveing a vehicle \\n request has been deleted...', 200);
            }

            $vehicle->update(['driver_id' => $user->id]);
            $request->update(['status' => 'approved']);

            //! send notificatoin to driver

            DB::commit();
            return $this->sudResponse('request approved successfully!', 200);

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), 500);
        }
    }

}
