<?php

namespace App\Services\Mobile\Transportation\Client;
use App\Models\Service;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use Exception;
use DB;


class TransportLuxuryService implements InterfaceTransport
{

    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

    //: Order Section
    public function createTransportOrder(Request $request)
    {
        $luxury = Service::where('name', 'luxury')->first();

        $user = auth()->user();

        DB::beginTransaction();
        try {
            $data = $request->validate([
                'source_latitude' => 'required|string',
                'source_longitude' => 'required|string',
                'destination_latitude' => 'required|string',
                'destination_longitude' => 'required|string',
                'note' => 'nullable'
            ]);
            $order = $user->order()->create(array_merge($data, ['service_id' => $luxury->id]));
            $order->destination()->create([
                'destination_latitude' => $data['destination_latitude'],
                'destination_longitude' => $data['destination_longitude']
            ]);

            //!send Notification

            DB::commit();
            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('error: ' . $e->getMessage(), 500);
        }

    }

    public function updatePriceOrder(Request $request, string $id)
    {
    }
    public function updateAutoAcceptOrder(bool $boolean, string $id)
    {
    }
    public function cancelOrder(string $id, Request $request)
    {
    }
    public function subscriptionOrder($id)
    {
    }


    //:Offer Section

    public function acceptOffer($id)
    {
    }
    public function rejectOffer(Request $request, string $id)
    {
    }

}
