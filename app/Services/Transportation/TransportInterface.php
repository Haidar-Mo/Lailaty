<?php

namespace App\Services\Transportation;

use Illuminate\Http\Request;

/**
 * Class TransportInterface.
 */
interface TransportInterface
{
    public function orderTransportService(Request $request); // For the client to order
    public function acceptOrder();  // For the driver to accept
    public function getPrice();      // Calculate the price
}
