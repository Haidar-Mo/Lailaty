<?php

namespace App\Services\Mobile;

use App\Models\Office;
use App\Models\User;
use App\Traits\HasFiles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Exception;

class OfficeRegistrationService
{
    use HasFiles;

    /**
     * Store each file inserted from user through the request
     * @param \App\Models\User $user
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(User $user, FormRequest $request)
    {
        $office = $user->office()->create($request->request->all());
        $uploadedPaths = [];
        foreach ($request->files as $key => $val) {
            $fileName = $this->saveFile($request->file($key), "OfficeDocuments");
            $uploadedPaths[$key] = $fileName;
        }
        if (!empty($uploadedPaths)) { 
            $office->document()->create($uploadedPaths);
        }
        return $office;
    }


    /**
     * Update the inserted documents
     * @param \App\Models\Office $office
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return string[]
     */
    public function update(Office $office, Request $request)
    {
        try {
            foreach ($request->files as $key => $value) {
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
