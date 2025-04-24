<?php

namespace App\Services\Mobile\Transportation\Client;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Service,
    OrderDuration,
    OrderOffer
};
use Illuminate\Support\Carbon;
use Exception;

/**
 * Class TransportWeddingService.
 */
class TransportWeddingService implements InterfaceTransport
{
    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

       //: Order Section

       public function createTransportOrder(Request $request)
       {
           $user = auth()->user();
           DB::beginTransaction();
           try {
               $wedding = Service::where('name', 'wedding')->first();
               $data = $request->validate([
                   'price' => 'required',
                   'source_latitude' => 'required|string',
                   'source_longitude' => 'required|string',
                   'destination' => 'required|array',
                   'destination.*.latitude' => 'required|string',
                   'destination.*.longitude' => 'required|string',
                   'female_driver' => 'boolean',
                   'date' => 'required|date',
                   'time' => 'required|date_format:H:i',
                   'number_of_days' => 'required|numeric',
                   'note' => 'nullable',
                   'wedding_category_id' => 'required',
               ]);
               $order = $user->order()->create(array_merge($data, ['service_id' => $wedding->id]));
               foreach ($data['destination'] as $value) {
                   $order->destination()->create([
                       'destination_latitude' => $value['latitude'],
                       'destination_longitude' => $value['longitude']
                   ]);
               }
               $startDate = Carbon::parse($data['date']);
               $endDate = $startDate->copy()->addDays($data['number_of_days']);
               OrderDuration::create([
                   'order_id' => $order->id,
                   'start' => $startDate,
                   'end' => $endDate,
                   'number_of_days' => $data['number_of_days']
               ]);
               DB::commit();
               return ['body' => $order];
           } catch (Exception $e) {
               DB::rollback();
               throw new Exception('error: ' . $e->getMessage(), 500);
           }
       }

       public function updatePriceOrder(Request $request, string $id)
       {
       }
       public function updateAutoAcceptOrder(bool $boolean, string $id)
       {
       }


        public function cancelOrder(string $id, Request $request){

           $user = auth()->user();
           $order = $user->order()->find($id);

           $data = $request->validate([
               'cancel_reason' => 'required',
           ]);
           if ($order->status == 'pending') {
                       $order->update([
                           'status' => 'canceled',
                           'cancel_reason' => $data['cancel_reason']
                       ]);
                       return response()->json('done');
               }
               return response()->json('!.... لا يمكن الغاء الطلب', 200);
           }



       public function subscriptionOrder($id)
       {
       }


       //:Offer Section

       public function acceptOffer($id)
    {
        $user = auth()->user();
        $orderOffer = OrderOffer::find($id);
        if ($orderOffer->status == 'pending') {
            $order = $orderOffer->order;
            if ($order) {
                $order->update([
                    'status' => 'accepted',
                    'vehicle_id' => $orderOffer->vehicle_id
                ]);
                $order->save();
                $orderOffer->update(['status' => 'accepted']);
                $offers = OrderOffer::where('order_id', $order->id)
                    ->where('id', '!=', $id)
                    ->get()
                ;
                foreach ($offers as $offer) {
                    $offer->delete();
                }
            }
            return response()->json(['message' => 'done'], 200);
        }
        return response()->json(['message' => '!.....العرض تم قبوله مسبقا']);
    }





    public function rejectOffer(Request $request, string $id)
    {
        $user = auth()->user();
        $orderOffer = OrderOffer::find($id);
        if ($orderOffer->status == 'pending') {
            $orderOffer->update([
                'status' => 'rejected',
            ]);
            return response()->json(['message' => 'done']);
        }
        return response()->json(['fail']);
    }


       public function getOrderOfferTransport()
    {

        $user = auth()->user();

        $orderOffersTravel = OrderOffer::with(['vehicle.driver', 'vehicle', 'user'])
            ->whereHas('order', function ($query) {
                $query->where('service_id', 1);
            })
            ->where('user_id', $user->id)
            ->orderBy('price', 'asc')
            ->get()
            ->map(function ($orderOffer) {
                $vehicle = $orderOffer->vehicle;
                $driverName = $vehicle->driver->first_name . ' ' . $vehicle->driver->last_name;
                $vehicleRate = $vehicle->rate;

                return [
                    'id' => $orderOffer->id,
                    'price' => $orderOffer->price,
                    'vehicle_id' => $orderOffer->vehicle_id,
                    'driver_name' => $driverName,
                    'vehicle_rate' => $vehicleRate,
                ];
            });


        return response()->json(['body' => $orderOffersTravel]);

    }
    public function showOffer($id){
        $offer=OrderOffer::where('id',$id)->get()
        ->map(function($orderOffer){
            $vehicle=$orderOffer->vehicle;
            $driverName=$vehicle->driver->first_name . ' ' . $vehicle->driver->last_name;
            $vehicleRate = $vehicle->rate;
            $vehicle_image=$vehicle->image;
            return [
                'id' => $orderOffer->id,
                'price' => $orderOffer->price,
                'vehicle_image' => $orderOffer->vehicle_id,
                'driver_name' => $driverName,
                'vehicle_rate' => $vehicleRate,
                'vehicle_image'=>$vehicle_image,
            ];

        });

        return ['body'=>$offer];

    }


       public function getOrderTransport(Request $request){

       }
}
