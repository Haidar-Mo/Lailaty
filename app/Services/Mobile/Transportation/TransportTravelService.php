<?php

namespace App\Services\Mobile\Transportation;
use App\Models\Service;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use Exception;
use DB;


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
                'source_latitude' => 'required|string',
                'source_longitude' => 'required|string',
                'destination_latitude' => 'required|string',
                'destination_longitude' => 'required|string',
                'date' => 'required|date',
                'time' => 'required',
                'price' => 'required',
                'female_driver' => 'boolean',
                'type' => 'required',
                'note' => 'nullable',
            ]);
            $order = $user->order()->create(array_merge($data, ['service_id' => $travel->id]));
            $order->destination()->create([
                'destination_latitude' => $data['destination_latitude'],
                'destination_lobgitude' => $data['destination_longitude']
            ]);

            //!send Notification

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('error: ' . $e->getMessage(), 500);
        }
    }

    public function updateOrder(Request $request, string $id)
    {
    }



    public function acceptTransportOrder(Request $request, string $id)
    {
    }

}
