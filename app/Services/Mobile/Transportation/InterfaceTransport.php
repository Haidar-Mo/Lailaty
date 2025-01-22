<?php

namespace App\Services\Mobile\Transportation;


use Illuminate\Http\Request;

/**
 * Class TransportInterface.
 */
interface InterfaceTransport
{
    //! Client Section
    public function orderTransportService(Request $request); // For the client to order

    public function updateOrder(Request $request, string $id);


    //! Driver section
    public function acceptTransportOrder(Request $request, string $id); //For Driver to accept order
}
