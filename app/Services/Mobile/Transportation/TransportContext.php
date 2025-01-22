<?php

namespace App\Services\Mobile\Transportation;

use Illuminate\Http\Request;

/**
 * Class TransportContext.
 */
class TransportContext
{

    public function __construct(private InterfaceTransport $transportType)
    {
    }

    public function orderTransportService(Request $request)
    {
        return $this->transportType->orderTransportService($request);
    }

    public function updateOrder(Request $request, string $id)
    {
        return $this->transportType->updateOrder($request, $id);

    }

    public function acceptTransportOrder(Request $request, string $id)
    {
        return $this->transportType->acceptTransportOrder($request, $id);
    }
}
