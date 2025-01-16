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

    
}
