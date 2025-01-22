<?php

namespace App\Services\Mobile\Transportation;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

/**
 * Class TransportWeddingService.
 */
class TransportWeddingService implements InterfaceTransport
{
    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

    public function orderTransportService(Request $request)
    {
    }


    public function updateOrder(Request $request, string $id)
    {
    }


    public function acceptTransportOrder(Request $request, string $id)
    {
    }
}
