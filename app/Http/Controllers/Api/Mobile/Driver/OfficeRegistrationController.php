<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficeCreateRequest;
use App\Rules\EgyptianPhoneNumber;
use App\Services\Mobile\OfficeRegistrationService;
use App\Traits\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeRegistrationController extends Controller
{
    use Responses;

    public function __construct(protected OfficeRegistrationService $service)
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
    //  * @return \Illuminate\Http\JsonResponse
     */
    public function store(OfficeCreateRequest $request)
    {
        $user = $request->user();
        if ($user->office()->first()) {
            return $this->sudResponse("User cannot have more than one office", 422);
        }
        try {
            $office = $this->service->create($user, $request);
            $office->append('documents');
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



}
