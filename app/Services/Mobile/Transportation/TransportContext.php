<?php

namespace App\Services\Mobile\Transportation;

use Illuminate\Http\Request;

/**
 * Class TransportContext.
 */
class TransportContext
{
    private InterfaceTransport $transportType;

    public function __construct(InterfaceTransport $transportType)
    {
        $this->transportType = $transportType;
    }

    public function orderTransportService(Request $request)
    {
        return $this->transportType->orderTransportService($request);
    }
    public function CancelOrderTransportService( $id){
        return $this->transportType->CancelOrderTransportService($id);
    }

    public function acceptTransportOrder(Request $request){
        return $this->transportType->acceptTransportOrder($request);
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



}
