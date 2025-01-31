<?php

namespace App\Services\Mobile\Transportation;

use App\Models\{
    OrderOffer,
    Subscription,
    Service,
    Order,
    Vehicle
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use Exception;
use Illuminate\Support\Facades\DB;
use NunoMaduro\Collision\Adapters\Phpunit\Subscribers\Subscriber;

class TransportTravelService implements InterfaceTransport
{

    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }
    public function orderTransportService(Request $request)
    {
        $travel = Service::where('name', 'travel')->first();
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $data = $request->validate([
                'source_latitude' => 'required|string',//x المصدر
                'source_longitude' => 'required|string',//y المصدر
                'destination_latitude' => 'required|string',// x الوجهة
                'destination_longitude' => 'required|string',// y الوجهة
                'date' => 'required|date',
                'number_of_seats'=>'required|numeric',
                'time' => 'required',
                'price' => 'required',
                'female_driver' => 'boolean',
                'type' => 'required',
                'note' => 'nullable',
            ]);
            $order = $user->order()->create(array_merge($data, ['service_id' => $travel->id]));
            $order->destination()->create([
                'destination_latitude' => $data['destination_latitude'],
                'destination_longitude' => $data['destination_longitude']
            ]);
            if ($data['type'] === 'shared') {
                Subscription::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                ]);
            }
            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('error: ' . $e->getMessage(), 500);
        }
    }


    public function getOrderTransport(){
      return response()->json('body', Order::where('type','shared')->where('status','pending')->with(['destination'])->get()) ;
    }


    public function subscriptionOrder($id){
        $user=auth()->user();
       $order=Order::find($id)->get();
       Subscription::create([
        'user_id'=>$user->id,
        'order_id'=>$order->id,
       ]);
       return response()->json('done subscription');

    }

    public function CancelOrderTransportService($id){
        $user=auth()->user();
        $order=$user->order()->find($id)->first();
        if($order->status=='pending'){
            if($order->type=='private'){
                $order->status='cancelled';
                $order->save();
                return response()->json('done');
            }
            if($order->type=='shared'){
                $count=Subscription::where('order_id',$order->id)->count();
                if($count>1){
                    Subscription::where('order_id',$order->id)->where('user_id',$user->id)->delete();
                    return response()->json('done');
                }else{
                $order->status='cancelled';
                $order->save();
                return response()->json('done');
                }

            }
        }
        return response()->json('!.... لا يمكن الغاء الطلب',200);

    }
//بدو تزبيط
    public function acceptTransportOrder(Request $request)
    {
        $captain = auth()->user();
        return $captain->id;
        DB::beginTransaction();

        try {

            $data = $request->validate([
                'price' => 'required',
                'order_id' => 'required',
                'user_id' => 'required',
            ]);

            $order = Order::findOrFail($data['order_id']);
            if (is_null($order->vehicle_id)) {
                $vehicle = Vehicle::where('driver_id', $captain->id)
                    ->where('service_type', 3)
                    ->first();

                if (!$vehicle) {
                    throw new Exception('No vehicle found for the driver with service type 3', 404);
                }

                $vehicleId = $vehicle->id;
            } else {
                $vehicleId = $order->vehicle_id;
            }


            OrderOffer::create([
                'user_id' => $data['user_id'],
                'order_id' => $data['order_id'],
                'vehicle_id' => $vehicleId,
                'price' => $data['price'],
            ]);

            DB::commit();
            return response()->json(['message' => 'done'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'error: ' . $e->getMessage()], 500);
        }
    }



    public function getOrderOfferTransport(){

    $user = auth()->user();

    $orderOffersTravil = OrderOffer::with(['vehicle.driver', 'vehicle','user'])
    ->whereHas('order', function($query) {
        $query->where('service_id', 3);
    })
    ->where('user_id', $user->id)
    ->orderBy('price', 'asc')
    ->get()
    ->map(function($orderOffer) {
        $vehicle = $orderOffer->vehicle;
        $driverName =  $vehicle->driver->first_name . ' ' . $vehicle->driver->last_name;
        $vehicleRate = $vehicle->rate ;

        return [
            'id'=>$orderOffer->id,
            'price' => $orderOffer->price,
            'vehicle_id' => $orderOffer->vehicle_id,
            'driver_name' => $driverName,
            'vehicle_rate' => $vehicleRate,
        ];
    });


    return response()->json(['body'=>$orderOffersTravil]);

}

public function acceptOrderOfferTransport($id)
{
    $user=auth()->user();
    $orderOffer=OrderOffer::find($id);
    if($orderOffer->status=='pending'){
    $order = $orderOffer->order;
    if ($order) {
        $order->update([
            'status' => 'accepted',
            'vehicle_id' => $orderOffer->vehicle_id
        ]);
        $order->save();
        $orderOffer->update(['status'=>'accepted']);
        $offers=OrderOffer::where('order_id', $order->id)
            ->where('id', '!=', $id)
            ->get()
            ;
            foreach($offers as $offer){
                $offer->delete();
            }
    }
    return response()->json(['message' => 'done'], 200);
}
return response()->json(['message'=>'!.....العرض تم قبوله مسبقا']);
}

public function updateOrderOffer(Request $request,$id){
    $user=auth()->user();
    $orderOffer=OrderOffer::find($id);
    if($orderOffer->status=='pending'){
    $data = $request->validate([
        'price' => 'nullable',

    ]);
    $orderOffer->update([
        'price'=>$data['price'],
    ]);
    return  response()->json(['message'=>'done']);
}
return response()->json(['message'=>'fail']);

}





}
