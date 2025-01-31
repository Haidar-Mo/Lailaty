<?php

namespace App\Services\Mobile\Transportation;

use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Class TransportContext.
 */
class TransportContext
{

    public function __construct(private InterfaceTransport $transportType)
    {
    }
    //: Client section
    public function orderTransportService(Request $request)
    {
        return $this->transportType->orderTransportService($request);
    }



    public function getOrderOfferTransport(){
        return $this->transportType->getOrderOfferTransport();

    }


    public function acceptOrderOfferTransport($id){
        return $this->transportType->acceptOrderOfferTransport($id);
    }
    public function updateOrderOffer(Request $request,$id){
        return $this->transportType->updateOrderOffer($request,$id);
    }





    public function updateOrder(Request $request, string $id)
    {
        return $this->transportType->updateOrder($request, $id);

    }

    public function cancelOrder(string $id, Request $request)
    {
        return $this->transportType->cancelOrder($id, $request);
    }


    //: Driver section
    public function acceptTransportOrder(Request $request, string $id)
    {
        return $this->transportType->acceptTransportOrder($request, $id);
    }

    public function cancelTransportOrder(Request $request, string $id)
    {
        return $this->transportType->cancelTransportOrder($request, $id);
    }

    public function cancelOrderOffer(Request $request,string $id){
        return $this->transportType->cancelOrderOffer($request,$id);

    }

    public function subscriptionOrder($id){
        return $this->transportType->subscriptionOrder($id);
    }

    public function finishTransportOrder(string $id){
        return $this->transportType->finishTransportOrder($id);
    }

}
