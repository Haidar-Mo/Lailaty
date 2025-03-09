<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use App\Rules\EgyptianPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use DB;
use Spatie\Permission\Models\Role;

class RegistrationController extends Controller
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
            'email' => ['required', 'unique:users,email', 'email'],
            'password' => ['required', 'min:6']
        ]);

        $verificationCode = random_int(100000, 999999);
        $expirationTime = Carbon::now()->addMinutes(10);
        try {
            DB::beginTransaction();

            $user = PendingUser::updateOrCreate(
                ['email' => $data['email']],
                [
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'verification_code' => $verificationCode,
                    'verification_code_expires_at' => $expirationTime
                ]
            );

            $user->notify(new VerificationCodeNotification($verificationCode));
            DB::commit();
            return response()->json(['message' => "Email registration done \n Verification email sent "], 200);
        } catch (Exception $e) {
            DB::rollback();
            report($e);
            return response()->json(['message' => "Error: some thing goes wrong... " . $e->getMessage()], 500);
        }
    }


    /**
     * Resend verification code for an deactivated existing email
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
            $verificationCode = random_int(100000, 999999);
            $expirationTime = Carbon::now()->addMinutes(10);
            $user->update([
                'verification_code' => $verificationCode,
                'verification_code_expires_at' => $expirationTime
            ]);
            $user->notify(new VerificationCodeNotification($verificationCode));
            DB::commit();
            return response()->json(['message' => 'Verification code has been re-sended'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'something goes wrong...'], 500);
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
        $user = PendingUser::where('email', $data['email'])->firstOrFail();

        if ($user->email_verified_at != null) {
            return response()->json(['message' => 'your Email is already verified'], 422);
        }
        if ($user->verification_code_expires_at <= now() || $user->verification_code != $data['verification_code']) {
            return response()->json(['message' => 'your verification code may be expired or it is incorrect'], 422);
        }

        try {
            DB::beginTransaction();

            $user->update([
                'email_verified_at' => now(),
                'verification_code_expires_at' => null
            ]);
            $new_user = User::create($user->only('email', 'password'));

            $accessToken = $new_user->createToken(
                'access_token',
                [TokenAbility::ACCESS_API->value],
                Carbon::now()->addMinutes(config('sanctum.ac_expiration'))
            );

            $refreshToken = $new_user->createToken(
                'refresh_token',
                [TokenAbility::ISSUE_ACCESS_TOKEN->value],
                Carbon::now()->addMinutes(config('sanctum.re_expiration'))
            );

            DB::commit();
            return response()->json([
                'message' => 'Email verified successfully',
                'access_token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'user' => $new_user,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => "Error: " . $e->getMessage()], 500);
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
                "phone_number" => ['required', 'string', 'unique:users,phone_number', new EgyptianPhoneNumber],
                "gender" => ['required', 'in:male,female'],
                "deviceToken" => ['nullable'],
                "city" => ['required'],
                "role" => ['required'],
                "can_create_office" => ['nullable']
            ]);

            $user = Auth::user();
            if ($user->is_full_registered)
                throw new Exception("your account is already full registered ", 422);
            $user->update($request->all());

            $role = Role::where('name', $request->role)
                ->where('guard_name', 'api')
                ->first();
            $user->assignRole($role);

            $user->load('roles');
            DB::commit();

            return response()->json([
                'message' => 'Registration is completely done',
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
