<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use App\Rules\EgyptionPhoneNumber;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use DB;
use Str;
class RegistratoinController extends Controller
{
    /**
     * Register an email into the application
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'unique:users,email', 'email']
        ]);
        try {
            DB::beginTransaction();

            $verificationCode = str::random(6);
            $expirationTime = Carbon::now()->addMinutes(10);

            $user = PendingUser::updateOrCreate(
                ['email' => $data['email']],
                [
                    'email' => $data['email'],
                    'verification_code' => $verificationCode,
                    'verification_code_expires_at' => $expirationTime
                ]
            );

            $user->notify(new VerificationCodeNotification($verificationCode));
            DB::commit();
            return response()->json(['message' => 'Email registration done. Verification email sent.'], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['messge' => "Error: ".$e->getMessage()], 500);
        }
    }


    /**
     * Resend verification code for an unactivatied existing email
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function resendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'exists:users,email'],
        ]);
        try {
            DB::beginTransaction();
            $user = PendingUser::where('email', $request->email)->firstOrFail();
            if ($user->hasVerifiedEmail())
                return response()->json(['message' => 'Email is already verified'], 405);
            $verificationCode = str::random(6);
            $expirationTime = Carbon::now()->addMinutes(10);
            $user->update([
                'verification_code' => $verificationCode,
                'verification_code_expires_at' => $expirationTime
            ]);
            $user->notify(new VerificationCodeNotification($verificationCode));
            DB::commit();
            return response()->json(['message' => 'Verification code has been resended'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'something gose wrong...'], 500);
        }
    }

    /**
     * Verify the previously registered email.
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:pending_users,email'],
            'verification_code' => ['required', 'string']
        ]);
        try {
            DB::beginTransaction();
            $user = PendingUser::where('email', $data['email'])->firstOrFail();

            if ($user->email_verified_at != null) {
                return response()->json(['message' => 'your Email is already verified'], 422);
            }
            if ($user->verification_code_expires_at <= now() || $user->verification_code != $data['verification_code']) {
                return response()->json(['message' => 'your verification code may be expired or it is incorrect'], 422);
            }

            $user->update([
                'email_verified_at' => now(),
                'verification_code_expires_at' => null
            ]);

            DB::commit();
            return response()->json(['message' => 'Email verified successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Register the rest of the information to the previously registered email.
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function informationRegistration(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                "first_name" => ['required', 'string'],
                "last_name" => ['required', 'string'],
                "email" => ['required', 'email', 'exists:pending_users,email'],
                "password" => ['required', 'confirmed', 'string', 'min:6'],
                "phone_number" => ['required', 'string', 'unique:users,phone_number', new EgyptionPhoneNumber],
                "gender" => ['required', 'in:male,female'],
                "deviceToken" => ['nullable'],
                "city" => ['required'],
                "role" => ['required'],
                "can_create_office" => ['nullable']
            ]);
            $pending_user = PendingUser::where('email', $request->email)->firstOrFail();

            if ($pending_user->email_verified_at == null)
                return response()->json([
                    'message' => 'your Email must be verified first'
                ]);

            $user = User::create($request->all());
            $pending_user->delete();
            $user->assignRole($request->role);

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
            DB::commit();

            return response()->json([
                'message' => 'Registration is completely done',
                'access_token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'user' => $user,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

    }
}
