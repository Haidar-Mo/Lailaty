<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleCreateRequest;
use App\Http\Requests\VehicleUpdateRequest;
use App\Models\Vehicle;
use App\Services\Mobile\DriverVehicleService;
use App\Traits\Responses;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
class VehicleController extends Controller
{
    use Responses;


    public function __construct(protected DriverVehicleService $driverVehicleService)
    {

    }

    /**
     * Retrieve all vehicles associated with the authenticated user.
     * 
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Auth::user()->vehicle()->get());
    }

    /**
     * Retrieve a specific vehicle associated with the authenticated user by its ID.
     *
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()->json(Auth::user()->vehicle()->findOrFail($id));
    }


    /**
     * Add a new vehicle for the authenticated user based on the specified vehicle type.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $vehicleType
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VehicleCreateRequest $request, $vehicleType)
    {
        try {
            $vehicle = $this->driverVehicleService->addNewVehicle($request, $request->user(), $vehicleType);
            $vehicle->load(['image', 'ownershipDocument']);
            return $this->indexOrShowResponse('vehicle', $vehicle, 201);
        } catch (Exception $e) {
            return $this->sudResponse('erroe: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Update the specified vehicle's details if the vehicle type matches.
     * @param \App\Http\Requests\VehicleUpdateRequest $request
     * @param mixed $vehicleType
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VehicleUpdateRequest $request, $vehicleType, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        if ($vehicle->vehicle_type != $vehicleType)
            return $this->sudResponse('Your vehicle is not the same type of request', 422);
        try {
            $updatedVehicle = $this->driverVehicleService->updateVehicleInfo($vehicle, $request);
            return $this->indexOrShowResponse('vehicle', $updatedVehicle, 200);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Update one of the specified vehicle's images.
     * @param \Illuminate\Http\Request $request
     * @param mixed $vehicleId
     * @param mixed $imageId
     * @return Vehicle|\Illuminate\Http\JsonResponse
     */
    public function updateImage(Request $request, $vehicleId, $imageId)
    {
        $vehicle = Auth()->user()->vehicle()->findOrFail($vehicleId);
        try {
            $image = $vehicle->image()->findOrFail($imageId);
            $vehicle = $this->driverVehicleService->updateImage($vehicle, $image, $request);
            $vehicle->load('image');
            return $this->indexOrShowResponse('vehicle', $vehicle, 200);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Update ownership documents of the specified vehicle
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOwnershipDocument(Request $request, $id)
    {
        $vehicle = Auth()->user()->vehicle()->find($id);
        $request->validate([
            'face_1' => 'required|image',
            'face_2' => 'required|image'
        ]);
        try {
            $vehicle = $this->driverVehicleService->updateVehicleOwnershipDocument($vehicle, $request);
            return $this->indexOrShowResponse('vehicle', $vehicle, 200);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Delete the specified vehicle from authenticated user's vehicles
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $vehicle = Auth::user()->vehicle()->findOrFail($id);
        $this->driverVehicleService->deleteVehicle($vehicle);
        return $this->sudResponse('vehicle has been deleted successfully!', 200);
    }


}
