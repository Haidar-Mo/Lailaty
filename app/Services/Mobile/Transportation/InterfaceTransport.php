<?php

namespace App\Services\Mobile\Transportation;

use Illuminate\Http\Request;

/**
 * Class TransportInterface.
 */
interface InterfaceTransport
{
    public function updateOrderOffer(Request $request,$id);
    public function acceptOrderOfferTransport($id);
    public function getOrderOfferTransport();
    public function acceptTransportOrder(Request $request);
    public function CancelOrderTransportService($id);
    public function orderTransportService(Request $request); // For the client to order
}
