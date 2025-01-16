<?php

namespace App\Http\Controllers\Api\Mobile\Client;

use App\Http\Controllers\Controller;
use App\Services\Mobile\Transportation\TransportContext;
use App\Services\Mobile\Transportation\TransportDriveLessonsService;
use App\Services\Mobile\Transportation\TransportLuxuryService;
use App\Services\Mobile\Transportation\TransportMoodService;
use App\Services\Mobile\Transportation\TransportShippingService;
use App\Services\Mobile\Transportation\TransportTaxiService;
use App\Services\Mobile\Transportation\TransportTravelService;
use App\Services\Mobile\Transportation\TransportWeddingService;
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
                'travle'=> new TransportTravelService($this->firebaseDatabase),
                'luxury'=> new TransportLuxuryService($this->firebaseDatabase),
                'wedding'=>new TransportWeddingService($this->firebaseDatabase),
                'mood'=>new TransportMoodService($this->firebaseDatabase),
                'shipping'=>new TransportShippingService($this->firebaseDatabase),
                'drive_lessons'=>new TransportDriveLessonsService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);

            $orderResponse = $context->orderTransportService($request);
            return $this->indexOrShowResponse('order', $orderResponse, 201);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }
}
