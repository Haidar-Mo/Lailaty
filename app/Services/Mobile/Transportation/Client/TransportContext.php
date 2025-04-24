<?php

namespace App\Services\Mobile\Transportation\Client;

use Illuminate\Http\Request;

/**
 * Class TransportContext.
 */
class TransportContext
{

    public function __construct(private InterfaceTransport $transportType)
    {
    }

    //: Order Section
    public function createTransportOrder(Request $request)
    {
        return $this->transportType->createTransportOrder($request);
    }

    public function updatePriceOrder(Request $request, string $id)
    {
        return $this->transportType->updatePriceOrder($request, $id);

    }

    public function updateAutoAcceptOrder(bool $boolean, string $id)
    {
        return $this->transportType->updateAutoAcceptOrder($boolean, $id);
    }

    public function cancelOrder(string $id, Request $request)
    {
        return $this->transportType->cancelOrder($id, $request);
    }

    public function subscriptionOrder($id)
    {
        return $this->transportType->subscriptionOrder($id);
    }


    //: Offer Section

    public function acceptOffer($id)
    {
        return $this->transportType->acceptOffer($id);
    }

    public function rejectOffer(Request $request, string $id)
    {
        return $this->transportType->rejectOffer($request, $id);
    }

    public function getOrderOfferTransport()
    {
        return $this->transportType->getOrderOfferTransport();
    }
    public function showOffer($id)
    {
        return $this->transportType->showOffer($id);
    }

    public function getOrderTransport(Request $request){
        return $this->transportType->getOrderTransport( $request);
    }


}
