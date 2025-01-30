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
use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use App\Traits\Responses;
use Exception;

class TransportationController extends Controller
{
    use Responses;

    public function __construct(private Database $firebaseDatabase)
    {
    }
    public function orderService(string $serviceType, Request $request)
    {

        try {
            $transportType = match ($serviceType) {
                'taxi' => new TransportTaxiService($this->firebaseDatabase),
                'travel' => new TransportTravelService($this->firebaseDatabase),
                'luxury' => new TransportLuxuryService($this->firebaseDatabase),
                'wedding' => new TransportWeddingService($this->firebaseDatabase),
                'mood' => new TransportMoodService($this->firebaseDatabase),
                'shipping' => new TransportShippingService($this->firebaseDatabase),
                'drive_lessons' => new TransportDriveLessonsService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);

            $orderResponse = $context->orderTransportService($request);
            return $this->indexOrShowResponse('order', $orderResponse, 201);
        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }


    public function updateOrder(Request $request, string $serviceType, string $id)
    {
        try {
            $transportType = match ($serviceType) {
                'taxi' => new TransportTaxiService($this->firebaseDatabase),
                'travel' => new TransportTravelService($this->firebaseDatabase),
                'luxury' => new TransportLuxuryService($this->firebaseDatabase),
                'wedding' => new TransportWeddingService($this->firebaseDatabase),
                'mood' => new TransportMoodService($this->firebaseDatabase),
                'shipping' => new TransportShippingService($this->firebaseDatabase),
                'drive_lessons' => new TransportDriveLessonsService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);

            $response = $context->updateOrder($request, $id);
            return $this->indexOrShowResponse('data', $response, 200);

        } catch (Exception $e) {

            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }


    public function cancelOrder(Request $request, string $serviceType, string $id)
    {
        try {
            $transportType = match ($serviceType) {
                'taxi' => new TransportTaxiService($this->firebaseDatabase),
                'travel' => new TransportTravelService($this->firebaseDatabase),
                'luxury' => new TransportLuxuryService($this->firebaseDatabase),
                'wedding' => new TransportWeddingService($this->firebaseDatabase),
                'mood' => new TransportMoodService($this->firebaseDatabase),
                'shipping' => new TransportShippingService($this->firebaseDatabase),
                'drive_lessons' => new TransportDriveLessonsService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);

            $response = $context->cancelOrder($id, $request);
            return $this->indexOrShowResponse('data', $response, 200);

        } catch (Exception $e) {

            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }
}
