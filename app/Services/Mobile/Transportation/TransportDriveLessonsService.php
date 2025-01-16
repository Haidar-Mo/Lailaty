<?php

namespace App\Services\Mobile\Transportation;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

/**
 * Class TransportDriveLessonsService.
 */
class TransportDriveLessonsService implements InterfaceTransport
{

    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

    public function orderTransportService(Request $request){}
}
