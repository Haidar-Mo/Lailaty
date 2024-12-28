<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Services\Mobile\DriverCarService;
use App\Traits\Responses;
use Illuminate\Http\Request;

class CarController extends Controller
{
    use Responses;


    public function __construct(protected DriverCarService $driverCarService)
    {

    }

    public function store(Request $request)
    {

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
