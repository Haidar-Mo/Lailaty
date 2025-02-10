<?php

namespace App\Services\Mobile\Transportation\Captain;
use App\Models\Service;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use Exception;
use DB;


class TransportLuxuryService implements InterfaceTransport
{

    private Database $firebaseDatabase;

    public function __construct(Database $firebaseDatabase)
    {
        $this->firebaseDatabase = $firebaseDatabase;
    }

    //: Order Section

    public function acceptTransportOrder(Request $request, string $id)
    {
    }

    public function cancelTransportOrder(Request $request, string $id)
    {
    }

    public function startTransportOrder(string $id)
    {
    }

    public function finishTransportOrder(string $id)
    {
    }


    //: Offer Section

    public function updatePriceOffer(Request $request, string $id)
    {
    }

    public function getOrderOfferTransport()
    {
    }

}
