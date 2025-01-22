<?php

namespace App\Services\Mobile\Transportation;

use App\Http\Requests\TransportRequest;
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

            throw new Exception($e->getMessage(), 422);
        }

    }


    public function cancelOrder(string $id)
    {
        //! do not forget to call it in interface and context first 
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
            $order = Order::findOrFail($id);
            $vehicle = auth()->user()->driveVehicle()->first();
            if (!$vehicle)
                throw new Exception('can not accept the order, no obtained car founded', 422);

            // check if the order is already accepted
            if ($order->status === "accepted")
                return throw new Exception('order is already accepted...', 422);

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







    //: Helper functions :

    //* prepare Firebase data

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


    //* push order to Firebase
    private function pushOrderToFirebase(bool $femaleDriver, array $orderData)
    {
        try {

            $node = $femaleDriver ? 'female_orders' : 'orders';
            return $this->firebaseDatabase->getReference($node)->push($orderData);

        } catch (\Kreait\Firebase\Exception\DatabaseException $e) {
            throw new Exception('Failed to update order: ' . $e->getMessage(), 500);
        }
    }



    //* Update firebase Order
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


    //* update Firebase with offers
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