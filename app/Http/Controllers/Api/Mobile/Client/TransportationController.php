<?php

namespace App\Http\Controllers\Api\Mobile\Client;

use App\Http\Controllers\Controller;
use App\Services\Mobile\Transportation\Client\TransportContext;
use App\Services\Mobile\Transportation\Client\TransportDriveLessonsService;
use App\Services\Mobile\Transportation\Client\TransportLuxuryService;
use App\Services\Mobile\Transportation\Client\TransportMoodService;
use App\Services\Mobile\Transportation\Client\TransportShippingService;
use App\Services\Mobile\Transportation\Client\TransportTaxiService;
use App\Services\Mobile\Transportation\Client\TransportTravelService;
use App\Services\Mobile\Transportation\Client\TransportWeddingService;
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

    //: Order Section
    public function createOrder(string $serviceType, Request $request)
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

            $orderResponse = $context->createTransportOrder($request);
            return $this->indexOrShowResponse('order', $orderResponse, 201);
        } catch (Exception $e) {

            $code = in_array($e->getCode(), ['422', '400']) ? $e->getCode() : '500';
            return $this->sudResponse('error: ' . $e->getMessage(), $code);
        }
    }

    public function updatePriceOrder(Request $request, string $serviceType, string $id)
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
            return $response = $context->updatePriceOrder($request, $id);

        } catch (Exception $e) {

            $code = in_array($e->getCode(), ['422', '400']) ? $e->getCode() : '500';
            return $this->sudResponse('error: ' . $e->getMessage(), $code);
        }
    }

    public function updateAutoAcceptOrder(bool $boolean, string $serviceType, string $id)
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

            $response = $context->updateAutoAcceptOrder($boolean, $id);
            return $this->indexOrShowResponse('data', $response, 200);

        } catch (Exception $e) {

            $code = in_array($e->getCode(), ['422', '400']) ? $e->getCode() : '500';
            return $this->sudResponse('error: ' . $e->getMessage(), $code);
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

            $code = in_array($e->getCode(), ['422', '400']) ? $e->getCode() : '500';
            return $this->sudResponse('error: ' . $e->getMessage(), $code);
        }
    }


    public function acceptOffer(string $serviceType, $id)
    {


        try {
            $transportType = match ($serviceType) {

                'travel' => new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);
            return $orderResponse = $context->acceptOffer($id);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }

    public function subscriptionOrder(string $serviceType, $id)
    {
        try {
            $transportType = match ($serviceType) {

                'travel' => new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);
            return $orderResponse = $context->subscriptionOrder($id);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }


    public function rejectOffer(string $serviceType, Request $request, $id)
    {
        try {
            $transportType = match ($serviceType) {

                'travel' => new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);
            return $orderResponse = $context->rejectOffer($request, $id);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
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

    public function showOffer(string $serviceType,$id)
    {
        try {
            $transportType = match ($serviceType) {

                'travel' => new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);
            return $orderResponse = $context->showOffer($id);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }

    public function getOrderTransport(string $serviceType,Request $request)
    {
        try {
            $transportType = match ($serviceType) {

                'travel' => new TransportTravelService($this->firebaseDatabase),
            };

            $context = new TransportContext($transportType);
            return $orderResponse = $context->getOrderTransport($request);

        } catch (Exception $e) {
            return $this->sudResponse('error: ' . $e->getMessage(), 500);
        }
    }







}
