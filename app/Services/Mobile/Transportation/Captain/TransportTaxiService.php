<?php

namespace App\Services\Mobile\Transportation\Captain;

use App\Models\Order;
use App\Models\Service;
use App\Models\Vehicle;
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

    //: Order Section

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

            $this->checkOrderValidation($order, $vehicle, ['pending']);

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

            $this->checkOrderValidation($order, $vehicle, ['pending', 'accepted']);
            $data = $request->validate([
                'cancel_reason' => 'required'
            ]);

            $order->update([
                'status' => 'cancel',
                'cancel_reason' => $data['cancel_reason']
            ]);
            $this->deleteFirebaseOrder($order);

            //DO: send notification to client
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

            $this->checkOrderValidation($order, $vehicle, ['accepted']);

            $order->update([
                'status' => 'delivering',
            ]);
            $this->updateFirebaseOrder($order, ['status' => 'delivering']);

            //DO: send notification to client

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

            $this->checkOrderValidation($order, $vehicle, ['delivering']);

            $order->update([
                'status' => 'ended',
            ]);
            $this->updateFirebaseOrder($order, ['status' => 'ended']);

            //DO: send notification to client

            DB::commit();
            return ['message' => 'order finished successfully!'];

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }



    //: Offer Section

    public function updatePriceOffer(Request $request, $id)
    {

    }


    public function getOrderOfferTransport()
    {
    }



    //: Helper functions :

    //** check if the order is mine and valid

    private function checkOrderValidation(Order $order, Vehicle $vehicle, array $status)
    {
        if (!$vehicle || $order->vehicle_id != $vehicle->id)
            throw new Exception('unauthenticated', 422);

        if (!$order || $order->status->not_in($status))
            throw new Exception('can not finish this order....', 422);

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