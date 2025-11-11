<?php

namespace App\Modules\Authentication\Interface\Http\Controllers\Api\V1;

use App\Core\Interface\Controllers\BaseController;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

//  public function __construct(
//         private RegisterUser $registerUser,
//         private LoginUser $loginUser
//     ) {}

//     public function register(Request $request)
//     {
//         $user = $this->registerUser->execute($request->email, $request->password);
//         return response()->json($user);
//     }

//     public function login(Request $request)
//     {
//         $token = $this->loginUser->execute($request->email, $request->password);
//         return response()->json(['token' => $token]);
//     }

class AuthController extends BaseController
{


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!$user->phone_verified_at) {
            return response()->json(['message' => 'Please verify your phone first.'], 403);
        }

        // Revoke old tokens (optional, to ensure one active session)
        $user->tokens()->delete();

        // Generate OTP
        $otp = rand(100000, 999999);
        $user->update([
            "otp_code" => (string) $otp,
            "otp_expires_at" => now()->addMinutes(10)
        ]);

        // Store in DB or cache (Redis) with expiration
        Cache::put('phone_', $request->phone, now()->addMinutes(5));

        // Send via SMS gateway (or fake for dev)
        // SmsService::send($request->phone, "Your OTP is: {$otp}");
        // TODO: send OTP via SMS or WhatsApp (for now just log it)
        info("OTP for {$user->phone}: {$otp}");


        return response()->json([
            'message' => 'Account created successfully. Please verify your phone.',
            'user' => $user,
            'opt' => $otp,
        ]);
    }

    /**
     * Register a new user and issue a personal access token (Sanctum).
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fullname'     => 'required|string|max:255',
            'phone'    => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $user = User::create([
            'fullname'  => $request->fullname,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'email'    => $request->email ?? null,
        ]);


        // Generate OTP
        $otp = rand(100000, 999999);
        $user->update([
            "otp_code" => (string) $otp,
            "otp_expires_at" => now()->addMinutes(10)
        ]);

        // Store in DB or cache (Redis) with expiration
        Cache::put('phone_', $request->phone, now()->addMinutes(5));

        // Send via SMS gateway (or fake for dev)
        // SmsService::send($request->phone, "Your OTP is: {$otp}");
        // TODO: send OTP via SMS or WhatsApp (for now just log it)
        info("OTP for {$user->phone}: {$otp}");


        return response()->json([
            'user' => $user,
            'message' => 'Account created successfully. Please verify your phone.',
        ]);
    }

    /**
     * Authenticate user and issue a personal access token. Only for OAuth..
     */
    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'phone'    => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // Ensure phone is verified
    //     $user = User::where('phone', $request->phone)->first();

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found.'], 404);
    //     }

    //     if (!$user->phone_verified_at) {
    //         return response()->json(['message' => 'Please verify your phone first.'], 403);
    //     }


    //       // Now generate Passport token
    //     $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
    //         'grant_type' => 'password',
    //         'client_id' => config('services.passport.password_client_id'),
    //         'client_secret' => config('services.passport.password_client_secret'),
    //         'username' => $user->phone,
    //         'scope' => '*',
    //     ]);

    //     // user:read orders:create

    //     return response()->json([
    //         'message' => 'User logged in successfully.',
    //         'token' => $response->json(),
    //         'user' => $user,
    //     ]);
    // }

    /**
     * Return authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Revoke current access token (logout).
     */
    // ---- Logout ----
    public function logout(Request $request)
    {

        $accessToken = $request->user()->token();

        // Revoke current token
        $accessToken->revoke();



        return response()->json(['message' => 'Logged out successfully.']);
    }




    public function verifyPhone(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string',
        ]);


        $cachedPhone = Cache::get('phone_');
        //get back to this later
        $user = User::where('phone', $cachedPhone)->first();


        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->otp_code !== $request->otp_code) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if ($user->otp_expires_at->isPast()) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        $user->update([
            'phone_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);


        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }



    public function logoutAll(Request $request)
    {
        $user = $request->user()->tokens->each(function ($token) {
            $token->revoke();
        });

        // Optional: revoke all refresh tokens
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $user->token->id)
            ->update(['revoked' => true]);

        return response()->json([
            'message' => 'Logged out from all devices'
        ]);
    }
}
