<?php

namespace App\Services\Mobile;


use App\Models\Image;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Exception;


class ProfileService
{

    /**
     * Handle the profile image upload and optimization.
     *
     * @param User $user
     * @param \Illuminate\Http\UploadedFile $image
     * @return string|null
     */
    public function uploadProfileImage(User $user, $image)
    {
        try {
            $filename = time() . '_profile_image_for_userID' . $user->id . '.' . $image->getClientOriginalExtension();

            // Store the image temporarily
            $tempPath = $image->storeAs('public/tmp', $filename);
            $tempFilePath = storage_path('app/' . $tempPath);

            // Optimize the image
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($tempFilePath);

            // Move to final location
            $finalPath = 'ProfileImages/' . $filename;
            Storage::disk('public')->move('tmp/' . $filename, $finalPath);

            // Delete the old image if it exists
            if ($user->image) {
                $oldImagePath = $user->image->path;
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            
            Image::updateOrCreate(
                ['imageable_id' => $user->id, 'imageable_type' => get_class($user)],
                ['path' => $finalPath, 'imageable_type' => get_class($user)]
            );

            return Storage::disk('public')->url($finalPath);
        } catch (Exception $e) {
            
            throw new Exception("Something went wrong while uploading the image: " . $e->getMessage());
        }
    }

    /**
     * Remove the profile image.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return void
     */
    public function removeProfileImage(User $user)
    {
        if ($user->image) {
            try {
                Storage::disk('public')->delete($user->image->path);
                $user->image()->delete();
            } catch (Exception $e) {
                throw new Exception("Failed to remove image: " . $e->getMessage());
            }
        } else {
            throw new Exception("Profile picture is already empty.");
        }
    }
}
