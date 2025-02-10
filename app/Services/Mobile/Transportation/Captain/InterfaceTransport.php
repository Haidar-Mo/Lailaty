<?php

namespace App\Services\Mobile\Transportation\Captain;


use Illuminate\Http\Request;

/**
 * Class TransportInterface.
 */
interface InterfaceTransport
{

    //: Order Section
    public function acceptTransportOrder(Request $request, string $id); //** accept order => give an offer or direct accept

    public function cancelTransportOrder(Request $request, string $id); //** Cancel the order

    public function startTransportOrder(string $id); //** Start the order

    public function finishTransportOrder(string $id); //** Finish the order

    //: Offer Section
    public function updatePriceOffer(Request $request, string $id);

    public function getOrderOfferTransport();

    // public function cancelOffer(Request $request, string $id);

}
