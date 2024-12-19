<?php

namespace App\Services\Mobile;

use App\Models\Office;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
class RegistrationStuffservice
{

    public function officeRegistration(User $user, array $data)
    {
        
        return $user->office()->create($data);
    }


    public function officeDocumentsRegistration(Office $office, Request $request)
    {
        try {
            $documents = [
                'tax_card' => 'tax_card_for_officeID',
                'commercial_registration_card' => 'commercial_registration_card_for_officeID',
                'insurance_card' => 'insurance_card_for_officeID',
                'value_added_tax_card' => 'value_added_tax_card_for_officeID',
                'attached_document' => 'attached_document_for_officeID'
            ];

            foreach ($documents as $document => $name) {
                if ($request->hasFile($document)) {
                    $fileName = time() . '_' . $name . $office->id . '-' . $request->file($document)->getClientOriginalExtension();

                    $tempPath = $request->file($document)->storeAs('public/tmp', $fileName);
                    $tempFilePath = storage_path('app/' . $tempPath);

                    $optimizerChain = OptimizerChainFactory::create();
                    $optimizerChain->optimize($tempFilePath);

                    $finalPath = 'OfficeDocuments/' . $fileName;
                    Storage::disk('public')->move('tmp/' . $fileName, $finalPath);

                    $uploadedPaths[$document] = $finalPath;
                }
                $office->documents()->Create($uploadedPaths);
                return $uploadedPaths;
            }


        } catch (Exception $e) {

            throw new Exception("Something went wrong while uploading the image: " . $e->getMessage());
        }
    }

}
