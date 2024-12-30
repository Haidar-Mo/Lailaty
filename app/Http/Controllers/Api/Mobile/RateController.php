<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\RateRequest;
use App\Services\Mobile\RateService;
use Illuminate\Http\Request;
use App\Models\{
    Rate,
    User,
    Vehicle
};
use Illuminate\Support\Facades\{
    Auth,
    DB
};
use App\Traits\Responses;
use Exception;

class RateController extends Controller
{
    use Responses;
    protected $rateService;

    public function __construct(RateService $rate)
    {
        $this->rateService = $rate;

    }

    public function RateClient(RateRequest $request, $id)
    {
        $user = User::find($id);
           return  DB::transaction(function () use ($request,$user) {
                $this->rateService->RateClientService($user,$request);
                return $this->sudResponse('! تم ارسال التقيم بنجاح ');
            });

    }

    public function RateCar(RateRequest $request, $id)
    {
        $car = Vehicle::find($id);
           return  DB::transaction(function () use ($request,$car) {
                $this->rateService->RateCarService($car,$request);
                return $this->sudResponse('! تم ارسال التقيم بنجاح ');
            });

    }
}
