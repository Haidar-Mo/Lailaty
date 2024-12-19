<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Services\Mobile\RegistrationStuffservice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterationStuffController extends Controller
{

    protected $registrationStuffService;


    public function __construct(RegistrationStuffservice $registrationStuffService)
    {
        $this->registrationStuffService = $registrationStuffService;
    }
    public function officeRegister(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone_number' => 'required',
            'email' => 'required|email',
            'Commercial_registration_number' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable'
        ]);

        $user = Auth::user();
        try {

            $office = $this->registrationStuffService->officeRegistration($user, $data);

            return response()->json([
                'success' => true,
                'message' => 'Office registered successfully!',
                'office' => $office,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function officeDocumentsRegister(Request $request)
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

            return response()->json([
                'success' => true,
                'message' => 'Documents have been registerd successfully!',
                'documents' => $documents
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

    }
}
