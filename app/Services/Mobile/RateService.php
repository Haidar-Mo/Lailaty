<?php

namespace App\Services\Mobile;
use App\Models\{
    Rate,
    User,
    Vehicle
};

use Exception;
use Illuminate\Support\Facades\{
    Auth,
    DB
};

/**
 * Class RateService.
 */
class RateService
{
    public function RateClientService($user,$request){
        try{

        DB::beginTransaction();
        $rate = new Rate();
        $rate->user_id = auth()->id();
        $rate->rate = $request->input('rate');
        $rate->description = $request->input('description');
        $rate->rateable_type = User::class;
        $rate->rateable_id = $user->id;
        $rate->save();
        DB::commit();

      }catch(Exception $e){
            DB::rollBack();
        }
    }

    public function RateCarService($car,$request){
        try{

        DB::beginTransaction();
        $rate = new Rate();
        $rate->user_id = auth()->id();
        $rate->rate = $request->input('rate');
        $rate->description = $request->input('description');
        $rate->rateable_type = Vehicle::class;
        $rate->rateable_id = $car->id;
        $rate->save();
        DB::commit();

      }catch(Exception $e){
            DB::rollBack();
        }
    }

}
