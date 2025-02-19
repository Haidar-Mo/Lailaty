<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Rules\EgyptianPhoneNumber;
use App\Services\Mobile\OfficeRegistrationService;
use App\Traits\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeRegistrationController extends Controller
{
    use Responses;

    public function __construct(protected OfficeRegistrationService $registrationStuffService)
    {

    }

    /**
     * Show the authenticated user's office
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = Auth::user();
        $office = $user->office()->with('document')->firstOrFail();
        return $this->indexOrShowResponse("office", $office, 200);
    }


    /**
     * Create a new office for the authenticated user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'unique:offices,phone_number', new EgyptianPhoneNumber],
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


    /**
     * Update an existing office for the authenticated user
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone_number' => ['sometimes', 'required', 'string', 'unique:offices,phone_number,' . $id, new EgyptianPhoneNumber],
            'email' => 'sometimes|required|email|unique:offices,email,' . $id,
            'commercial_registration_number' => 'sometimes|required|string|unique:offices,commercial_registration_number,' . $id,
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $office = $user->office()->where('id', $id)->first();

        if (!$office) {
            return $this->sudResponse("Office not found or not owned by user", 404);
        }

        try {
            $office->update($data);
            return $this->indexOrShowResponse("office", $office, 200);
        } catch (Exception $e) {
            return $this->sudResponse($e->getMessage(), 500);
        }
    }


    /**
     * Store the documents for a previously created office for the authenticated user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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


    /**
     * Update the office documents for authenticated user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOfficeDocument(Request $request)
    {
        $request->validate([
            'tax_card' => 'sometimes|image',
            'commercial_registration_card' => 'sometimes|image',
            'insurance_card' => 'sometimes|image',
            'value_added_tax_card' => 'sometimes|image',
            'attached_document' => 'sometimes|image',
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
