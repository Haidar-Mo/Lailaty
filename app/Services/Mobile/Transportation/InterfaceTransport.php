<?php

namespace App\Services\Mobile\Transportation;


use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Class TransportInterface.
 */
interface InterfaceTransport
{

    public function updateOrderOffer(Request $request,$id);
    public function acceptOrderOfferTransport($id);
    public function getOrderOfferTransport();
    public function cancelOrderOffer(Request $request,string $id);
    public function subscriptionOrder($id);
    public function finishTransportOrder(string $id);


    //: Client Section
    public function orderTransportService(Request $request); //* Order new Transport service

    public function updateOrder(Request $request, string $id); //* Update order price

    public function cancelOrder(string $id, Request $request); //* Cancel the order


    //: Driver section
    public function acceptTransportOrder(Request $request, string $id); //* accept order => give an offer or direct accept

    public function cancelTransportOrder(Request $request, string $id);


}
