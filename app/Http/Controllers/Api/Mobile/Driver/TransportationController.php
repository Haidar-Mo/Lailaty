<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Services\Mobile\Transportation\Captain\TransportContext;
use App\Services\Mobile\Transportation\Captain\TransportDriveLessonsService;
use App\Services\Mobile\Transportation\Captain\TransportLuxuryService;
use App\Services\Mobile\Transportation\Captain\TransportMoodService;
use App\Services\Mobile\Transportation\Captain\TransportShippingService;
use App\Services\Mobile\Transportation\Captain\TransportTaxiService;
use App\Services\Mobile\Transportation\Captain\TransportTravelService;
use App\Services\Mobile\Transportation\Captain\TransportWeddingService;
use App\Traits\Responses;
use Exception;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class TransportationController extends Controller
{

    use Responses;

    public function __construct(private Database $firebaseDatabase)
    {
    }

    //: Order Section
    public function acceptOrder(Request $request, string $serviceType, string $id)
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

            return $response = $context->acceptTransportOrder($request, $id);
            //return $this->indexOrShowResponse('data', $response, 201);
        } catch (Exception $e) {

            return $this->sudResponse($e->getMessage(), $e->getCode());
        }


    }

    public function cancelTransportOrder(Request $request, string $serviceType, string $id)
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

            return $response = $context->cancelTransportOrder($request, $id);
            // return $this->indexOrShowResponse('data', $response, 201);
        } catch (Exception $e) {

            return $this->sudResponse($e->getMessage(), 500);
        }


    }


    public function finishTransportOrder(string $serviceType, string $id)
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

            $response = $context->finishTransportOrder($id);
            return $this->indexOrShowResponse('data', $response, 201);
        } catch (Exception $e) {

            return $this->sudResponse($e->getMessage(), 500);
        }


    }


    //: Offer Section

    public function updatePriceOffer(Request $request, string $serviceType, string $id)
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

            $response = $context->updatePriceOffer($request, $id);
            return $this->indexOrShowResponse('data', $response, 201);

        } catch (Exception $e) {

            return $this->sudResponse($e->getMessage(), 500);
        }
    }

    public function getOrderOfferTransport(string $serviceType)
    {
        try {
            $transportType = match ($serviceType) {

                'travel' => new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);


            return $orderResponse = $context->getOrderOfferTransport();

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }
}
