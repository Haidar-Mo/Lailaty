<?php

namespace App\Services\Mobile\Transportation;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

/**
 * Class TransportWeddingService.
 */
class TransportWeddingService
{
    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

    public function orderTransportService(Request $request){}
}
