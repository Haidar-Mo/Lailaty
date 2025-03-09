<?php

namespace App\Services\Mobile\Transportation\Captain;

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


    //بدو تزبيط
    //تقديم عرض للزبون

    public function acceptTransportOrder(Request $request, $id)
    {
        $captain = auth()->user();

        DB::beginTransaction();

        try {

            $data = $request->validate([
                'price' => 'required',
                'user_id' => 'required',
            ]);

            $order = Order::findOrFail($id);
            if (is_null($order->vehicle_id)) {
                $vehicle = Vehicle::where('driver_id', $captain->id)
                    ->whereHas('service', function ($query) {
                        $query->where('service_id', 3);
                    })
                    ->first();
                if (!$vehicle) {
                    throw new Exception('No vehicle found for the driver', 404);
                }

                $vehicleId = $vehicle->id;
            } else {
                $vehicleId = $order->vehicle_id;
            }

            OrderOffer::create([
                'user_id' => $data['user_id'],
                'order_id' => $order->id,
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

    //الكابتن يلغي الطلب
    public function cancelTransportOrder(Request $request, $id)
    {
        $user = auth()->user();
        $order = Order::find($id);
        $data = $request->validate(['cancel_reason' => 'required']);
        if ($order->status == 'pending') {
            $order->update([
                'status' => 'cancelled',
                'cancel_reason' => $data['cancel_reason'],
            ]);
            return response()->json(['message' => 'done']);
        }
        return response()->json(['message' => 'fail']);
    }

    public function startTransportOrder(string $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::find($id);
            $vehicle = auth()->user()->driveVehicle()->first();

            if (!$vehicle || $order->vehicle_id != $vehicle->id)
                throw new Exception('unauthenticated', 422);

            if (!$order || !in_array($order->status, ['accepted']))
                throw new Exception('can not finish this order....', 200);


            $order->update([
                'status' => 'delivering',
            ]);

            //DO: send notification to client

            DB::commit();
            return ['message' => 'order started successfully!'];

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function finishTransportOrder(string $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::find($id);
            $vehicle = auth()->user()->driveVehicle()->first();

            if (!$vehicle || $order->vehicle_id != $vehicle->id)
                throw new Exception('unauthenticated', 422);

            if (!$order || !in_array($order->status, ['delivering']))
                throw new Exception('can not finish this order....', 200);

            $order->update([
                'status' => 'ended',
            ]);

            //DO: send notification to client

            DB::commit();
            return ['message' => 'order finished successfully!'];

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    //: Offer Section

    public function getOrderTransport()
    {
        return ['body' => Order::where('type', 'shared')->where('status', 'pending')->with(['destination'])->get()];
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


    /*  
    * تم تعليق هذا التابع لللتأكد من إمكانية تعديل العرض قبل رفضه
    
    public function updatePriceOffer(Request $request, $id)
    {
        $user = auth()->user();
        $orderOffer = OrderOffer::find($id);
        if ($orderOffer->status == 'pending') {
            $data = $request->validate([
                'price' => 'nullable',

            ]);
            $orderOffer->update([
                'price' => $data['price'],
            ]);
            return response()->json(['message' => 'done']);
        }
        return response()->json(['message' => 'fail']);

    }*/


    //يقوم الكابتن بالتعديل على عرضه الذي تم رفضه من قبل الزبون

    public function updatePriceOffer(Request $request, string $id)
    {
        $captain = auth()->user();
        $orderOffer = OrderOffer::find($id);
        $data = $request->validate(['price' => 'required']);
        if ($orderOffer->status == 'rejected') {
            $orderOffer->update(['price' => $data['price'], 'status' => 'pending']);
            return response()->json(['message' => 'done']);
        }
        return response()->json(['message' => 'fail']);


    }





}
