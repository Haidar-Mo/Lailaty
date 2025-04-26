<?php

namespace App\Services\Mobile\Transportation\Client;

use App\Models\{
    OrderOffer,
    Subscription,
    Service,
    Order,
    Vehicle
};
use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\DB;
use Exception;

class TransportTravelService implements InterfaceTransport
{

    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

    //: Order Section

    public function createTransportOrder(Request $request)
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
                'number_of_seats' => 'required|numeric',
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

    public function updateAutoAcceptOrder(bool $boolean, string $id)
    {
        //! Empty ?
    }
    public function updatePriceOrder(Request $request, string $id)
    {
        //! Empty ?
    }

    //العميل يلغي طلبه
    public function cancelOrder(string $id, Request $request)
    {
        $user = auth()->user();
        $order = $user->order()->find($id);

        $data = $request->validate([
            'cancel_reason' => 'required',
        ]);
        if ($order->status == 'pending') {
            if ($order->type == 'private') {
                $order->update([
                    'status' => 'canceled',
                    'cancel_reason' => $data['cancel_reason']
                ]);
                return response()->json('done');
            }
            if ($order->type == 'shared') {
                $count = Subscription::where('order_id', $order->id)->count();
                if ($count > 1) {
                    Subscription::where('order_id', $order->id)->where('user_id', $user->id)->delete();
                    return response()->json('done');
                } else {
                    $order->update([
                        'status' => 'canceled',
                        'cancel_reason' => $data['cancel_reason']
                    ]);
                    return response()->json('done');
                }

            }
        }
        return response()->json('!.... لا يمكن الغاء الطلب', 200);

    }




    public function subscriptionOrder($id)
    {
        $user = auth()->user();
        $order = Order::find($id);
        if ($order->type == 'shared' && $order->status == 'pending') {
            Subscription::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
            ]);
            return response()->json(['message' => 'done subscription']);
        }
        return response()->json(['message' => 'fail']);

    }

    public function getOrderOfferTransport()
    {

        $user = auth()->user();

        $orderOffersTravel = OrderOffer::with(['vehicle.driver', 'vehicle', 'user'])
            ->whereHas('order', function ($query) {
                $query->where('service_id', 3);
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
//يعرض تفاصيل العرض
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
    $user=auth()->user();
    $orders=$user->order()->where('status',$request->status)->with(['destination'])->get();
    return ['body'=>$orders];

   }



    //: Offer Section

    //قبول عرض الكابتن من قبل الزبون

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


    //العميل يرفض العرض

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


    //بدنا تابع بجييب تفاصيل العرض من صورة سيارة الى نقطة البداية و النهاية و تاريخ الطلب
    //تابع بجيب الطلبات يلي عنا يلي تمت و يلي قيد التنفيذ
    //


}
