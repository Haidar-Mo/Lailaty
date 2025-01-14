<?php

namespace App\Services\Mobile;

use App\Models\Image;
use App\Models\Vehicle;
use App\Traits\HasFiles;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use DB;
use Exception;

/**
 * Class DriverCarService.
 */
class DriverVehicleService
{
    use HasFiles;
    public function addNewVehicle(Request $request, User $user, $vehicleType)
    {
        DB::beginTransaction();
        try {
            $images = [];
            $ownership_docs = [];

            $imageFields = ['image_1', 'image_2', 'image_3', 'image_4', 'image_5'];
            $ownershipFields = ['face_1', 'face_2'];

            foreach ($request->files as $fieldName => $file) {
                if (in_array($fieldName, $imageFields)) {
                    $images[$fieldName] = $request->file($fieldName);
                } elseif (in_array($fieldName, $ownershipFields)) {
                    $ownership_docs[$fieldName] = $request->file($fieldName);
                }
            }
            $excludeFields = array_merge($imageFields, $ownershipFields);
            $request->vehicleType = $vehicleType;
            $vehicle = $user->vehicle()->create($request->except($excludeFields));

            foreach ($images as $key => $value) {
                $path = $this->saveFile($value, 'Vehicle');
                $vehicle->image()->create(['path' => $path]);
            }
            foreach ($ownership_docs as $key => $value) {
                $path = $this->saveFile($value, 'Vehicle');
                $paths[$key] = $path;
            }
            $vehicle->ownershipDocument()->create($paths);
            DB::commit();
            return $vehicle;

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Something went wrong : " . $e->getMessage(), 500);
        }
    }

    public function updateVehicleInfo(Vehicle $vehicle, FormRequest $request)
    {
        DB::beginTransaction();
        try {
            $vehicle = $vehicle->update($request->all());
            DB::commit();
            return $vehicle;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function updateImage(Vehicle $vehicle, Image $image, Request $request)
    {
        DB::beginTransaction();
        try {
            $newImage = $request->file('image');
            if ($image) {
                $this->deleteFile($image->path);
            }
            $path = $this->saveFile($newImage, 'Vehicle');
            $image->update(['path' => $path]);
            DB::commit();
            return $vehicle;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function updateVehicleOwnershipDocument(Vehicle $vehicle, Request $request)
    {
        DB::beginTransaction();
        try {
            $old_docs = $vehicle->ownershipDocument;
            $files = $request->files;
            foreach ($files as $key => $value) {
                $this->deleteFile($old_docs->$key);
                $paths[$key] = $this->saveFile($request->file($key), 'Vehicle');
            }
            $vehicle->ownershipDocument()->update($paths);
            DB::commit();
            return $vehicle;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function deleteVehicle(Vehicle $vehicle)
    {
        DB::beginTransaction();
        try {
            // Delete images record
            $images = $vehicle->image;
            foreach ($images as $image) {
                $fileToDelete[] = $image->path;
            }
            $image->delete();

            // Delete ownership document record
            $doc = $vehicle->ownershipDocument;
            if ($doc) {
                $fileToDelete[] = $doc->face_1;
                $fileToDelete[] = $doc->face_2;
                $doc->delete();
            }

            $vehicle->delete();
            DB::commit();
            foreach ($fileToDelete as $value) {
                $this->deleteFile($value);
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), 500);
        }

    }

}
