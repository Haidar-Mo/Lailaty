<?php

namespace App\Traits;

use File;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Log;


/**
 * Files like : images , videos , ...etc
 */
trait HasFiles
{
    // public function setFileName(array $images, string $folder_name): array
    // {
    //     return array_map(function ($image) use ($folder_name) {
    //         return $folder_name . '/' . uniqid() . '_' . substr(
    //             str_shuffle(
    //                 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_',
    //             ),
    //             0,
    //             50
    //         ) . '.' . $image->getClientOriginalExtension();
    //     }, $images);
    // }

    // public function saveFile1(array $images, array $names, string $path)
    // {
    //     for ($i = 0; $i < count($images); $i++) {
    //         $images[$i]->storeAs($path, $names[$i]);

    //     }
    // }

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
