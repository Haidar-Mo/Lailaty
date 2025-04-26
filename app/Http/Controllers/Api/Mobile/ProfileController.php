<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Mobile\ProfileService;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;


class ProfileController extends Controller
{

    use Responses;
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
        $user->load('roles');
        return $this->indexOrShowResponse('user', $user, 200);

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
        $user = $request->user();
        try {
            $this->profileImageService->uploadProfileImage($user, $request->file('image'));
            $user->load('image');
            $image = $user->image;
            return $this->indexOrShowResponse('image', $image, 200);
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
