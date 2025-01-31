<?php

namespace App\Services\Mobile\Transportation;
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


    public function orderTransportService(Request $request)
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
                'destination_lobgitude' => $data['destination_longitude']
            ]);

            //!send Notification

            DB::commit();
            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('error: ' . $e->getMessage(), 500);
        }
        
    }
}
