<?php

namespace App\Http\Controllers\Api\Mobile\Driver;

use App\Http\Controllers\Controller;
use App\Traits\{
    Responses,
    HasFiles
};
use App\Models\User;
use App\Services\Mobile\RegistrationDocuments;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\{
    Documents,
    UpdateDocuments

};
use Illuminate\Support\Facades\{
    Auth,
    DB
};

class DocumentsRegisterController extends Controller
{
    use Responses, HasFiles;

    public function __construct(protected RegistrationDocuments $registration)
    {
        
    }


    public function store(Documents $request)
    {
        $user = Auth::user();
        if ($user->registrationDocument()->first())
            return $this->sudResponse("الوثائق مسجلة لديك بالفعل", 422);
        $validatedData = $request->validated();

        try {
            DB::transaction(function () use ($validatedData, $user) {
                $this->registration->DocumentsRegistration($validatedData, $user);
            });
            return $this->sudResponse("تم تسجيل الوثائق بنجاح !", 200);
        } catch (Exception $e) {

            return $this->sudResponse($e->getMessage(), 500);
        }
    }

    public function update(UpdateDocuments $request)
    {
        $user = auth()->user();
        $existingDocument = $user->registrationDocument;
        DB::beginTransaction();
        try {
            $this->registration->updateDocumentsRegistration($request->all(), $existingDocument);
            DB::commit();
            return $this->sudResponse("! تم تحديث الوثائق بنجاح ", 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->sudResponse($e->getMessage(), 500);
        }
    }







}



