<?php

namespace App\Http\Controllers\Api\Mobile\Client;

use App\Http\Controllers\Controller;
use App\Services\Transportation\TransportContext;
use App\Services\Transportation\TransportTaxiService;
use App\Traits\Responses;
use Exception;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class TransportationController extends Controller
{
    use Responses;
    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }
    public function orderService(string $serviceType, Request $request)
    {

        try {
            $transportType = match ($serviceType) {
                'taxi' => new TransportTaxiService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);

            $orderResponse = $context->orderTransportService($request);
            return $this->indexOrShowResponse('order', $orderResponse, 201);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }
}
