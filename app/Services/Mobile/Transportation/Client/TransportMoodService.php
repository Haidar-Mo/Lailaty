<?php

namespace App\Services\Mobile\Transportation\Client;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

/**
 * Class TransportMoodService.
 */
class TransportMoodService implements InterfaceTransport
{
    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

       //: Order Section

       public function createTransportOrder(Request $request)
       {
       }
       public function updatePriceOrder(Request $request, string $id)
       {
       }
       public function updateAutoAcceptOrder(bool $boolean, string $id)
       {
       }
       public function cancelOrder(string $id, Request $request)
       {
       }
       public function subscriptionOrder($id)
       {
       }
   
   
       //:Offer Section
   
       public function acceptOffer($id)
       {
       }
       public function rejectOffer(Request $request, string $id)
       {
       }
}
