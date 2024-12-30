<?php

namespace App\Services\Mobile;

use App\Models\Vehicle;
use App\Traits\HasFiles;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

/**
 * Class DriverCarService.
 */
class DriverVehicleService
{
    use HasFiles;
    public function addNewVehicle(Request $request, User $user)
    {
        try {
            $images = $request->allFiles();
            $vehicle = $user->vehicle()->create($request->all());
            foreach ($images as $image => $value) {
                $path = $this->saveFile($value, 'Vehicle');
                $vehicle->image()->create(['path' => $path]);
            }
            return $vehicle;

        } catch (Exception $e) {
            throw new Exception("Something went wrong while uploading the image: " . $e->getMessage(), 500);
        }
    }


    public function updateVehicleInfo()
    {
    }


    public function removeVehicle()
    {
    }

    public function createDriveVehicleRequest()
    {
    }


    public function deleteMyDriveVehicleRequest()
    {
    }


    public function respondeToDriveVehicleRequest()
    {
    }
}
