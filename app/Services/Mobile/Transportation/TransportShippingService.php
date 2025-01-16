<?php

namespace App\Services\Mobile\Transportation;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

/**
 * Class TransportShippingService.
 */
class TransportShippingService
{
    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

    public function orderTransportService(Request $request){}
}
