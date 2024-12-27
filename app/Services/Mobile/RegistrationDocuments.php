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
        $fileNames = $this->setFileName(array_values($document), 'DocumentsRegistration');
        foreach ($document as $key => $file) {
             $this->saveFile([$file], [$fileNames[array_search($key, array_keys($document))]], 'public');

            $uploadedPaths[$key] = $fileNames[array_search($key, array_keys($document))];
        }
        $user->registrationDocument()->create($uploadedPaths);
        DB::commit();
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['message' => '! حدث خطأ ما ']);
    }
}


public function updateDocumentsRegistration(array $files, $existingDocument){
    $oldFiles = [];
    try{
     DB::beginTransaction();
    foreach ($files as $key => $newFile) {
        if ($newFile) {
            $currentFile = $existingDocument->$key;
            $newFileName = $this->setFileName([$newFile], 'Documents')[0];

            if ($currentFile) {
                $oldFiles[] = $currentFile;
                $existingDocument->update([$key => $newFileName]);
            }

            $this->saveFile([$newFile], [$newFileName], 'public');
        }
    }
    if (!empty($oldFiles)) {
        $this->deleteFile('Documents', $oldFiles);
    }
    DB::commit();
}catch(Exception $e){
    DB::rollBack();
}
}



}
