<?php

namespace App\Services\Mobile\Transportation;

use Illuminate\Http\Request;

/**
 * Class TransportInterface.
 */
interface InterfaceTransport
{
    public function orderTransportService(Request $request); // For the client to order
}
