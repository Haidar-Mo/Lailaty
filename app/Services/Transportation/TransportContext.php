<?php

namespace App\Services\Transportation;

use Illuminate\Http\Request;

/**
 * Class TransportContext.
 */
class TransportContext
{
    private TransportInterface $transportType;

    public function __construct(TransportInterface $transportType) {
        $this->transportType = $transportType;
    }

    public function orderTransportService(Request $request) {
        return $this->transportType->orderTransportService($request);
    }

    public function acceptOrder() {
        return $this->transportType->acceptOrder();
    }

    public function getPrice($distance, $carType) {
        return $this->transportType->getPrice();
    }
}
