<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Rules\EgyptionPhoneNumber;
use App\Services\Mobile\OfficeRegistrationService;
use App\Traits\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeRegisterationController extends Controller
{
    use Responses;

    public function __construct(protected OfficeRegistrationService $registrationStuffService)
    {

    }

    public function show()
    {
        $user = Auth::user();
        $office = $user->office()->with('document')->first();
        return $this->indexOrShowResponse("office", $office, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'unique:offices,phone_number', new EgyptionPhoneNumber],
            'email' => 'required|email|unique:offices,email',
            'commercial_registration_number' => 'required|string|unique:offices,commercial_registration_number',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = Auth::user();

        if ($user->office()->first()) {
            return $this->sudResponse("User cannot have more than one office", 422);
        }
        try {

            $office = $user->office()->create($data);

            return $this->indexOrShowResponse("office", $office, 200);

        } catch (Exception $e) {
            return $this->sudResponse($e->getMessage(), 500);
        }
    }

    public function storeOfficeDocument(Request $request)
    {
        $request->validate([
            'tax_card' => 'required|image',
            'commercial_registration_card' => 'required|image',
            'insurance_card' => 'required|image',
            'value_added_tax_card' => 'required|image',
            'attached_document' => 'nullable|image',
        ]);

        $user = Auth::user();
        $office = $user->office;
        try {

            $documents = $this->registrationStuffService->officeDocumentsRegistration($office, $request);

            return $this->indexOrShowResponse("documents", $documents, 200);

        } catch (Exception $e) {
            return $this->sudResponse($e->getMessage(), 500);
        }

    }

    public function updateOfficeDocument(Request $request)
    {
        $request->validate([
            'tax_card' => 'nullable|image',
            'commercial_registration_card' => 'nullable|image',
            'insurance_card' => 'nullable|image',
            'value_added_tax_card' => 'nullable|image',
            'attached_document' => 'nullable|image',
        ]);

        $user = Auth::user();
        $office = $user->office;
        try {

            $documents = $this->registrationStuffService->UpdateOfficeDocument($office, $request);

            return $this->indexOrShowResponse("documents", $documents, 200);

        } catch (Exception $e) {
            return $this->sudResponse($e->getMessage(), 500);
        }
    }


}
