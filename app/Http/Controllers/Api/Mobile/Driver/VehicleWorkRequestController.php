<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleWorkRequest;
use App\Services\Mobile\DriverWorkService;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Exception;

class VehicleWorkRequestController extends Controller
{
    use Responses;


    public function __construct(protected DriverWorkService $driverWorkService)
    {
    }

    /**
     * Display list of fleet owners.
     * @return mixed
     */
    public function indexFleetOwner()
    {
        return User::role('fleetOwner', 'api')->with('office')->get();
    }

    /**
     * Display list of available car of a specified fleet owner.
     * @param string $id
     * @return mixed
     */
    public function indexAvailableVehicle(string $id)
    {
        $fleet_owner = User::role('fleetOwner', 'api')->findOrFail($id);
        return $fleet_owner->vehicle()->where('available', 1)->get();
    }

    /**
     * Send work request to specified fleet owner.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(string $id)
    {
        try {
            $user = auth()->user();
            $vehicle = Vehicle::findOrFail($id);
            $request = $this->driverWorkService->createWorkRequest($vehicle, $user);

            return $this->indexOrShowResponse('request', $request, 201);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delte the authanticated user's specified request before it get accepted or rejected
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(string $id)
    {
        try {
            $request = auth()->user()->workRequest()
                ->where('status', 'pending')
                ->where('id', $id)
                ->first();
            if ($request === null) {
                return $this->sudResponse('sorre, we can not find your request.... try later', 422);
            }
            $request->delete();
            return $this->sudResponse('Your request has been deleted successfully!', 200);
        }
    }



    /**
     * Display list of authanticated user's vehicle's requests
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return auth()->user()
            ->receivedWorkRequest()->with('vehicle')
            ->get();
    }

    /**
     * Show the specified request
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Eloquent\Relations\HasMany[]
     */
    public function show(string $id)
    {
        return auth()->user()->receivedWorkRequest()->findOrFail($id);
    }


    public function approve(string $id)
    {
        try {
            $request = auth()->user()->receivedWorkRequest()->findOrFail($id);
            $vehicle = $request->vehicle();
            $driver = $request->user();
            return $this->driverWorkService->approveWorkRequest($request, $vehicle, $driver);
        } catch (Exception $e) {
            return $this->sudResponse($e->getMessage(), 500);
        }
    }

    /**
     * Reject the specified request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(string $id)
    {
        $request = auth()->user()->receivedWorkRequest()->findOrFail($id);
        if ($request->status != 'pending')
            return $this->sudResponse('the request is already processed');

        $request->update(['status' => 'rejected']);
        return $this->sudResponse('request rejected..');
    }
}
