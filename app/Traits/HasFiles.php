<?php

namespace App\Traits;
use Illuminate\Http\UploadedFile;


/**
 * Files like : images , videos , ...etc
 */
trait HasFiles
{
    
    public function saveFile(UploadedFile $file, string $folder_name)
    {
        $file_name = "$folder_name/" . time() . '_' . substr(
            str_shuffle(
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_',
            ),
            0,
            50
        ) . '.' . $file->getClientOriginalExtension();
        $file->storeAs("public/", $file_name);
        return $file_name;
    }
    public static function deleteFile(string $name)
    {
        $filePath = storage_path('app/public/' . $name);
        if (file_exists($filePath) && $name) {
            unlink($filePath);
        }
    }
}
