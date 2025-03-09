<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt for a user.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'exists:users,email'],
            'password' => ['required', 'string'],
            'deviceToken' => ['sometimes']
        ]);
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {

            return response()->json(['message' => 'Please check the email and password'], 401);
        }
        $user = User::find(Auth::user()->id);
        if ($request->has('deviceToken')) {
            $user->update(['deviceToken' => $request->deviceToken]);
        }
        $user->tokens()->delete();

        $accessToken = $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value],
            Carbon::now()->addMinutes(config('sanctum.ac_expiration'))
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            Carbon::now()->addMinutes(config('sanctum.re_expiration'))
        );

        $user->load('roles');
        return response()->json([
            'message' => 'Logged in successfully ',
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'user' => $user,
        ], 200);
    }


    /**
     * Delete all user's access token and log the user out of the application.
     * 
     * @return JsonResponse
     */
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json(null, 200);
    }

    /**
     * Refresh an out of date token.
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $accessToken = $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value],
            Carbon::now()->addMinutes(config('sanctum.ac_expiration'))
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            Carbon::now()->addMinutes(config('sanctum.rt_expiration'))
        );
        return response()->json([
            'message' => '.Token created successfully ',
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'user' => $user,
        ]);
    }


}
