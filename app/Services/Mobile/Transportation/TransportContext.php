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

    public function updateOrder(Request $request, string $id)
    {
        return $this->transportType->updateOrder($request, $id);

    }

    public function updateAutoAccept(bool $boolean, string $id)
    {
        return $this->transportType->updateAutoAccept($boolean, $id);
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

}
