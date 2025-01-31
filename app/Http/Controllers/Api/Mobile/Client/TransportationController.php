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

use App\Models\{
    OrderOffer,
    Subscription,
    Service,
    Order
};

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
            return $response = $context->updateOrder($request, $id);
            //return $this->indexOrShowResponse('data', $response, 200);

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

            return $response = $context->cancelOrder($id, $request);
            //return $this->indexOrShowResponse('data', $response, 200);

        } catch (Exception $e) {

            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }

    public function getOrderOfferTransport(string $serviceType)
    {


        try {
            $transportType = match ($serviceType) {

                'travel'=> new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);


            return $orderResponse = $context->getOrderOfferTransport();

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }



    public function acceptOrderOfferTransport(string $serviceType,$id)
    {


        try {
            $transportType = match ($serviceType) {

                'travel'=> new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);


            return $orderResponse = $context->acceptOrderOfferTransport($id);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }

    public function updateOrderOffer(string $serviceType,Request $request,$id)
    {


        try {
            $transportType = match ($serviceType) {

                'travel'=> new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);


            return $orderResponse = $context->updateOrderOffer($request,$id);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }


    public function subscriptionOrder(string $serviceType,$id)
    {


        try {
            $transportType = match ($serviceType) {

                'travel'=> new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);


            return $orderResponse = $context->subscriptionOrder($id);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }




    public function cancelOrderOffer(string $serviceType,Request $request,$id)
    {


        try {
            $transportType = match ($serviceType) {

                'travel'=> new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);


            return $orderResponse = $context->cancelOrderOffer($request,$id);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }




}
