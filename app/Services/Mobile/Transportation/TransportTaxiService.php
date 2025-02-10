<?php

namespace App\Services\Mobile\Transportation;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use Exception;
use DB;

/**
 * Class TransportTaxiService.
 */
class TransportTaxiService implements InterfaceTransport
{

    public function __construct(private Database $firebaseDatabase)
    {

    }

    //: Client section

    /**
     * Send a Taxi Order
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return array
     */
    public function orderTransportService(Request $request)
    {
        $taxi = Service::where('name', 'taxi')->first();
        $user = auth()->user();


        DB::beginTransaction();
        try {

            $activeOrder = auth()->user()
                ->order()
                ->where('status', '!=', 'ended')
                ->first();
            if ($activeOrder)
                throw new Exception("You have an active Taxi order, please finish it first ", 422);

            $data = $request->validate(rules: [
                'price' => 'required',
                'source_latitude' => 'required|string',
                'source_longitude' => 'required|string',
                'destination' => 'required|array',
                'destination.*.latitude' => 'required|string',
                'destination.*.longitude' => 'required|string',
                'female_driver' => 'boolean',
                'auto_accept' => 'nullable',
                'note' => 'nullable',
            ]);

            $order = $user->order()->create(array_merge($data, ['service_id' => $taxi->id]));
            foreach ($data['destination'] as $value) {
                $order->destination()->create([
                    'destination_latitude' => $value['latitude'],
                    'destination_longitude' => $value['longitude']
                ]);
            }
            $order_data = $this->prepareOrderData($order, $user);

            $firebase_order = $this->pushOrderToFirebase($data['female_driver'], $order_data);

            $order->update([
                'reference_key' => $firebase_order->getKey(),
            ]);

            //do: send Notification

            DB::commit();
            return ["order" => $order, "message" => "order has been sended"];

        } catch (Exception $e) {
            DB::rollback();
            if (!in_array($e->getCode(), ['422']))
                report($e);
            throw $e;
        }
    }


    /**
     * Update a Taxi order
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @throws \Exception
     * @return array
     */
    public function updateOrder(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $order = auth()->user()->order()
                ->where('status', 'pending')
                ->where('id', $id)
                ->first();

            if ($order == null)
                throw new Exception('no pending order founded..!', 422);


            $data = $request->validate([
                'price' => 'required|numeric',
            ]);

            $order->update($data);
            $this->updateFirebaseOrder($order, $data);
            DB::commit();
            return ['order' => $order, 'message' => 'order has been updated'];
        } catch (Exception $e) {
            DB::rollback();
            if (!in_array($e->getCode(), ['422']))
                report($e);

            throw $e;
        }

    }


    /**
     * Update the Auto-Accept choice
     * @param bool $boolean
     * @param string $id
     * @throws \Exception
     * @return array
     */
    public function updateAutoAccept(bool $boolean, string $id)
    {
        DB::beginTransaction();
        try {
            $order = auth()->user()->order()
                ->where('status', 'pending')
                ->where('id', $id)
                ->first();

            if ($order == null)
                throw new Exception('no pending order founded..!', 422);
            $order->update(['auto_accept' => $boolean]);
            DB::commit();
            return ['order' => $order, 'message' => 'order has been updated'];
        } catch (Exception $e) {
            DB::rollback();
            if (!in_array($e->getCode(), ['422']))
                report($e);

            throw $e;
        }
    }


    /**
     * Cancel a Taxi Order
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return string[]
     */
    public function cancelOrder(string $id, Request $request)
    {
        DB::beginTransaction();
        try {
            $order = auth()->user()->order()
                ->where('id', $id)
                ->whereNotIn('status', ['delivering', 'ended'])
                ->first();

            if (!$order)
                throw new Exception('can not cancel this order...', 422);

            $data = $request->validate([
                'cancel_reason' => 'required',
            ]);

            $order->update([
                'status' => 'canceled',
                'cancel_reason' => $data['cancel_reason']
            ]);

            //do: send notification if there is an driver 

            $this->deleteFirebaseOrder($order);
            DB::commit();
            return ['message' => 'Order canceled successfully!'];

        } catch (Exception $e) {
            DB::rollback();
            if (!in_array($e->getCode(), ['422']))
                report($e);
            throw $e;
        }

    }




    //: Driver Section


    /**
     * Accept a Taxi order
     * -For Driver user-
     * @param mixed $request
     * @param mixed $id
     * @throws \Exception
     * @return mixed|string[]|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse
     */
    public function acceptTransportOrder(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($id);
            $vehicle = auth()->user()->driveVehicle()->first();

            if (!$vehicle || $order->vehicle_id != $vehicle->id)
                throw new Exception('unauthenticated', 422);

            if (!$order || $order->status != 'pending')
                throw new Exception('error: can not accept this order....', 422);

            $request->validate([
                'price' => 'required|numeric'
            ]);

            if ($order->auto_accept && (float) $order->price === (float) $request->price) {
                $order->update([
                    'user_id' => auth()->user()->id,
                    'status' => 'accepted'
                ]);
                $this->updateFirebaseOrder($order, ['status' => 'accepted']);

                $order->offer()->delete();
                DB::commit();
                return ['message' => 'order has been accepted'];
            }


            $offer = $order->offer()->create([
                'user_id' => $order->user_id,
                'vehicle_id' => $vehicle->id,
                'price' => $request->price

            ]);

            //* update the firebase database record
            $this->updateFirebaseOffers($order, $offer);

            DB::commit();
            return ['offer' => $offer, 'message' => 'the offer has been sended to client'];

        } catch (Exception $e) {
            DB::rollback();
            if (!in_array($e->getCode(), ['422']))
                report($e);
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }


    /**
     * Cancel a Taxi order
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @throws \Exception
     * @return string[]
     */
    public function cancelTransportOrder(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::find($id);
            $vehicle = auth()->user()->driveVehicle()->first();

            if (!$vehicle || $order->vehicle_id != $vehicle->id)
                throw new Exception('unauthenticated', 422);

            if (!$order || $order->status->not_in(['pending', 'accepted']))
                throw new Exception('can not cancel this order....', 422);

            $data = $request->validate([
                'cancel_reason' => 'required'
            ]);

            $order->update([
                'status' => 'cancel',
                'cancel_reason' => $data['cancel_reason']
            ]);
            $this->deleteFirebaseOrder($order);

            //do: send notification to client
            DB::commit();
            return ['message' => 'order canceled successfully!'];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Start delivering the client
     * @param string $id
     * @throws \Exception
     * @return string[]
     */
    public function startTransportOrder(string $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::find($id);
            $vehicle = auth()->user()->driveVehicle()->first();

            if (!$vehicle || $order->vehicle_id != $vehicle->id)
                throw new Exception('unauthenticated', 422);

            if (!$order || $order->status->not_in(['canceled', 'pending', 'delivering', 'ended']))
                throw new Exception('can not start this order....', 422);

            $order->update([
                'status' => 'delivering',
            ]);
            $this->updateFirebaseOrder($order, ['status' => 'delivering']);

            //do: send notification to client

            DB::commit();
            return ['message' => 'order started successfully!'];

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    /**
     * Finish delivering the client
     * @param string $id
     * @throws \Exception
     * @return string[]
     */
    public function finishTransportOrder(string $id)
    {
        DB::beginTransaction();
        try {
            $order = Order::find($id);
            $vehicle = auth()->user()->driveVehicle()->first();

            if (!$vehicle || $order->vehicle_id != $vehicle->id)
                throw new Exception('unauthenticated', 422);

            if (!$order || $order->status->not_in(['accepted', 'canceled', 'pending', 'ended']))
                throw new Exception('can not finish this order....', 422);

            $order->update([
                'status' => 'ended',
            ]);
            $this->updateFirebaseOrder($order, ['status' => 'ended']);

            //do: send notification to client

            DB::commit();
            return ['message' => 'order finished successfully!'];

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }



    //: Helper functions :

    //** prepare Firebase data

    private function prepareOrderData($order, $user)
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name . " " . $user->last_name,
                'image' => $user->image_url,
                'rate' => $user->rate,
            ],
            'price' => $order->price,
            'source_latitude' => $order->source_latitude,
            'source_longitude' => $order->source_longitude,
            'destination' => $order->destination,
            'note' => $order->note ?? '',
            'status' => 'pending',
            'created_at' => now()->toISOString(),
        ];
    }


    //** push order to Firebase
    private function pushOrderToFirebase(bool $femaleDriver, array $orderData)
    {
        try {

            $node = $femaleDriver ? 'female_orders' : 'orders';
            return $this->firebaseDatabase->getReference($node)->push($orderData);

        } catch (\Kreait\Firebase\Exception\DatabaseException $e) {
            throw new Exception('Failed to update order: ' . $e->getMessage(), 500);
        }
    }


    //** Update firebase Order
    private function updateFirebaseOrder(Order $order, array $data)
    {
        try {

            $node = $order->female_driver ? 'female_orders' : 'orders';
            $this->firebaseDatabase
                ->getReference("$node/{$order->reference_key}")
                ->update($data);

        } catch (\Kreait\Firebase\Exception\DatabaseException $e) {
            throw new Exception('Failed to update order: ' . $e->getMessage(), 500);
        }
    }


    //** Delete firebase Order
    private function deleteFirebaseOrder(Order $order)
    {
        try {

            $node = $order->female_driver ? 'female_orders' : 'orders';
            $this->firebaseDatabase
                ->getReference("$node/{$order->reference_key}")
                ->remove();

        } catch (\Kreait\Firebase\Exception\DatabaseException $e) {
            throw new Exception('Failed to cancel order: ' . $e->getMessage(), 500);
        }
    }

    //** update Firebase with offers
    private function updateFirebaseOffers(Order $order, $offer)
    {
        try {
            $offerData = [
                'vehicle' => [
                    'driver_name' => auth()->user()->first_name . " " . auth()->user()->last_name,
                    'rate' => $offer->vehicle()->first()->rate,
                    'order_count' => $offer->vehicle()->first()->order_count,
                ],
                'price' => $offer->price,
            ];

            $node = $order->female_driver ? 'female_orders' : 'orders';
            $this->firebaseDatabase
                ->getReference("$node/{$order->reference_key}/offers")
                ->push($offerData);

        } catch (\Kreait\Firebase\Exception\DatabaseException $e) {
            throw new Exception('Failed to update order: ' . $e->getMessage(), 500);
        }
    }
}