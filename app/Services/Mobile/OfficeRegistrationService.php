<?php

namespace App\Services\Mobile;

use App\Models\Office;
use App\Traits\HasFiles;
use App\Traits\Responses;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
class OfficeRegistrationService
{
    use HasFiles;

    /**
     * Store each file inserted from user throught the request
     * @param \App\Models\Office $office
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return string[]
     */
    public function officeDocumentsRegistration(Office $office, Request $request)
    {
        try {
            foreach ($request->files as $key => $val) {
                $fileName = $this->saveFile($request->file($key), "OfficeDocuments");
                $uploadedPaths[$key] = $fileName;
            }
            $office->document()->create($uploadedPaths);
            return $uploadedPaths;

        } catch (Exception $e) {
            throw new Exception("Something went wrong while uploading the image: " . $e->getMessage());
        }
    }


    /**
     * Update the inserted documents
     * @param \App\Models\Office $office
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return string[]
     */
    public function UpdateOfficeDocument(Office $office, Request $request)
    {
        try {
            foreach ($request->files as $key => $vlaue) {
                $fileName = $this->saveFile($request->file($key), "OfficeDocuments");
                if ($office->document->$key)
                    $this->deleteFile($office->document->$key);
                $uploadedPaths[$key] = $fileName;
            }
            $office->document()->update($uploadedPaths);
            return $uploadedPaths;

        } catch (Exception $e) {
            throw new Exception("Something went wrong while uploading the image: " . $e->getMessage());
        }
    }

}
