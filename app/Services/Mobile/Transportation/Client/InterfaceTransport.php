<?php

namespace App\Services\Mobile\Transportation\Client;


use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Class TransportInterface.
 */
interface InterfaceTransport
{



    //: Order Section
    public function createTransportOrder(Request $request); //** Order new Transport service

    public function updatePriceOrder(Request $request, string $id); //** Update order price

    public function updateAutoAcceptOrder(bool $boolean,string $id); //** update auto-accept choice

    public function cancelOrder(string $id, Request $request); //** Cancel the order

    public function subscriptionOrder($id);

    public function showOffer($id);
    public function getOrderOfferTransport();
    public function getOrderTransport(Request $request);

    //: Offer Section

    public function acceptOffer($id);

    public function rejectOffer(Request $request,string $id);


}
