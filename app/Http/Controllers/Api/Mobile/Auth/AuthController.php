<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\EgyptionPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use DB;
use Exception;
use Str;

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
            'phone_number' => ['required', 'string', 'exists:users,phone_number', new EgyptionPhoneNumber],
            'password' => ['required', 'string'],
            'deviceToken' => ['nullable']
        ]);
        $credentials = $request->only('phone_number', 'password');

        if (!Auth::attempt($credentials)) {

            return response()->json(['message' => 'يرجى التحقق من كلمة المرور أو رقم الهاتف'], 401);
        }
        $user = User::find(Auth::user()->id);
        if ($request->has('deviceToken')) {
            $user->update(['deviceToken' => $request->deviceToken]);
        }
        $user->tokens()->delete();

        $accessToken = $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value, $user->roles()->first()->name],
            Carbon::now()->addMinutes(config('sanctum.ac_expiration'))
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            Carbon::now()->addMinutes(config('sanctum.re_expiration'))
        );

        $user->load('roles');
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
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
            [TokenAbility::ACCESS_API->value, 'role:' . $user->roles->first()->name],
            Carbon::now()->addMinutes(config('sanctum.ac_expiration'))
        );

        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            Carbon::now()->addMinutes(config('sanctum.rt_expiration'))
        );
        return response()->json([
            'message' => '.تم إنشاء الرمز بنجاح',
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'user' => $user,
        ]);
    }


}
