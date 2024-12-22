<?php

namespace App\Traits;


/**
 * Files like : images , videos , ...etc
 */
trait HasFiles
{
    public function setFileName(array $images, string $folder_name): array
    {
        return array_map(function ($image) use ($folder_name) {
            return $folder_name . '/' . uniqid() . '_' . substr(
                str_shuffle(
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_',
                ),
                0,
                50
            ) . '.' . $image->getClientOriginalExtension();
        }, $images);
    }

    public function saveFile(array $images, array $names, string $path)
    {
        for ($i = 0; $i < count($images); $i++) {
            $images[$i]->storeAs($path, $names[$i]);
        }
    }

    public static function deleteFile(string $path, array $names)
    {
        foreach ($names as $name) {
            $filePath = storage_path('app/public/' . $name);
            if (file_exists($filePath) && $name) {
                unlink($filePath);
            }
        }
    }
}
