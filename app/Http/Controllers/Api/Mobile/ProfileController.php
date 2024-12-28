<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Services\Mobile\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;


class ProfileController extends Controller
{

    public function __construct(protected ProfileService $profileImageService)
    {

    }


    /**
     * Displays the authenticated user's profile along with their image.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'messgae' => 'showing profile',
            'user' => $user
        ]);
    }


    /**
     * Allows the user to upload a new profile image
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function newProfileImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image'],
        ]);
        $user = Auth::user();
        try {
            $imageUrl = $this->profileImageService->uploadProfileImage($user, $request->file('image'));

            return response()->json([
                'success' => true,
                'message' => 'Image profile changed successfully!',
                'path' => $imageUrl,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while uploading the image.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deletes the authenticated user's profile image
     * 
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function removeProfileImage()
    {
        $user = auth()->user();

        try {
            $this->profileImageService->removeProfileImage($user);

            return response()->json([
                'success' => true,
                'message' => 'profile image removed successfully!'
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
