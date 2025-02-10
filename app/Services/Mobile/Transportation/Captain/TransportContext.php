<?php

namespace App\Services\Mobile\Transportation\Captain;

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
    public function acceptTransportOrder(Request $request, string $id)
    {
        return $this->transportType->acceptTransportOrder($request, $id);
    }

    public function cancelTransportOrder(Request $request, string $id)
    {
        return $this->transportType->cancelTransportOrder($request, $id);
    }

    public function startTransportOrder(string $id)
    {
        return $this->transportType->startTransportOrder($id);
    }

    public function finishTransportOrder(string $id)
    {
        return $this->transportType->finishTransportOrder($id);
    }

    //: Offer Section
    /* public function cancelOffer(Request $request, string $id)
     {
         return $this->transportType->cancelOffer($request, $id);

     }*/

    public function updatePriceOffer(Request $request, string $id)
    {
        return $this->transportType->updatePriceOffer($request, $id);
    }

    public function getOrderOfferTransport()
    {
        return $this->transportType->getOrderOfferTransport();
    }

}
