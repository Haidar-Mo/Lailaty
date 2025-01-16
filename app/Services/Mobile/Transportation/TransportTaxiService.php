<?php

namespace App\Services\Mobile\Transportation;

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

    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

    public function orderTransportService(Request $request)
    {
        $taxi = Service::where('name', 'taxi')->first();
        $user = auth()->user();
        DB::beginTransaction();
        try {
            $data = $request->validate(rules: [
                'price' => 'required',
                'source_latitude' => 'required|string',
                'source_longitude' => 'required|string',
                'destination' => 'required|array',
                'destination.*.latitude' => 'required|string',
                'destination.*.longitude' => 'required|string',
                'female_driver' => 'boolean',
                'note' => 'nullable',
            ]);
            $order = $user->order()->create(array_merge($data, ['service_id' => $taxi->id]));
            foreach ($data['destination'] as $value) {
                $order->destination()->create([
                    'destination_latitude' => $value['latitude'],
                    'destination_longitude' => $value['longitude']
                ]);
            }
            $orderData = [
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

            if ($data['female_driver']) {

                $this->firebaseDatabase
                    ->getReference('female_orders')
                    ->push($orderData);
                DB::commit();
                return $order;
            }
            $this->firebaseDatabase
                ->getReference('orders')
                ->push($orderData);
            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("error:" . $e->getMessage(), 500);
        }
    }

}
