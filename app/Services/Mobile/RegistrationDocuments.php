<?php

namespace App\Services\Mobile;

use App\Traits\HasFiles;
use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\{
    Auth,
    DB
};
class RegistrationDocuments
{
    use HasFiles;

    public function DocumentsRegistration($document, $user)
    {
        $uploadedPaths = [];

        try {
            DB::beginTransaction();
            foreach ($document as $key => $file) {
                $fileName = $this->saveFile($file, "RegistirationDocuments");
                $uploadedPaths[$key] = $fileName;
            }
            $user->registrationDocument()->create($uploadedPaths);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function updateDocumentsRegistration(array $files, $existingDocument)
    {
        $oldFiles = [];
        try {
            DB::beginTransaction();
            foreach ($files as $key => $newFile) {
                if ($newFile) {
                    $currentFile = $existingDocument->$key;

                    $newFileName = $this->saveFile($newFile, "RegistirationDocuments");

                    if ($currentFile) {
                        $oldFiles[] = $currentFile;
                        $existingDocument->update([$key => $newFileName]);
                    }

                }
            }
            if (!empty($oldFiles)) {
                foreach ($oldFiles as $file)
                    $this->deleteFile($file);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }



}
