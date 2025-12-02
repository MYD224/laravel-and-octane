<?php

namespace App\Modules\Authentication\Interface\Http\Controllers\Api\V1;

use App\Core\Interface\Controllers\BaseController;
use App\Modules\Authentication\Application\Services\UserService;
use App\Modules\Authentication\Application\V1\Commands\GenerateOtpCommand;
use App\Modules\Authentication\Application\V1\Commands\LogoutCommand;
use App\Modules\Authentication\Application\V1\Commands\RegisterUserCommand;
use App\Modules\Authentication\Application\V1\Commands\UpdateUserProfileCommand;
use App\Modules\Authentication\Application\V1\Commands\VerifyOtpCommand;
use App\Modules\Authentication\Application\V1\Handlers\GenerateOtpHandler;
use App\Modules\Authentication\Application\V1\UseCases\GenerateOtpUseCase;
use App\Modules\Authentication\Application\V1\UseCases\LogoutUseCase;
use App\Modules\Authentication\Application\V1\UseCases\RegisterUserUseCase;
use App\Modules\Authentication\Application\V1\UseCases\UpdateUserProfileUseCase;
use App\Modules\Authentication\Application\V1\UseCases\VerifyOtpUseCase;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;



class AuthController extends BaseController
{


    public function login(Request $request, UserService $userService)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $userData = $userService->findByPhone($request->phone);

        if (!$userData) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!$userData->phoneVerifiedAt) {
            return response()->json(['message' => 'Please verify your phone first.'], 403);
        }

        // Revoke old tokens (optional, to ensure one active session)
        $userService->deleteTokens($userData->phone);

        // Generate OTP
        // $otp = rand(100000, 999999);
        $otp = app(GenerateOtpHandler::class)->handle(
            new GenerateOtpCommand($userData->id)
        );
        // $user->update([
        //     "otp_code" => $result['otp'],
        //     "otp_expires_at" => now()->addMinutes(10)
        // ]);

        // Store in DB or cache (Redis) with expiration
        // Cache::put('phone_', $request->phone, now()->addMinutes(5));

        // Send via SMS gateway (or fake for dev)
        // SmsService::send($request->phone, "Your OTP is: {$otp}");
        // TODO: send OTP via SMS or WhatsApp (for now just log it)
        // info("OTP for {$user->phone}: {$otp}");


        return response()->json([
            'message' => 'Account created successfully. Please verify your phone.',
            'user' => $userData,
            'opt' => $otp,
        ]);
    }

    public function register(
        Request $request,
        RegisterUserUseCase $useCase,
        UpdateUserProfileUseCase $updateProfileUseCase,
    ) {

        try {

            $validator = Validator::make($request->all(), [
                'fullname'     => 'required|string|max:255',
                'phone'    => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $command = new RegisterUserCommand(
                fullname: $request->fullname,
                phone: $request->phone,
                email: $request->email ?? 'kalili@gmail.com',
                password: $request->password
            );

            $userData = $useCase->execute($command);

            // Generate OTP
            $otp = app(GenerateOtpUseCase::class)->execute(
                new GenerateOtpCommand($userData->id)
            );

            $updateCommande = new UpdateUserProfileCommand(
                id: $userData->id,
                otpCode: (string) $otp['otp'],
                otpExpiresAt: CarbonImmutable::now()->addMinutes($otp['ttl'])
            );

            $userData = $updateProfileUseCase->execute($updateCommande);


            // Send via SMS gateway (or fake for dev)
            // SmsService::send($request->phone, "Your OTP is: {$otp}");
            // TODO: send OTP via SMS or WhatsApp (for now just log it)
            info("OTP for {$userData->phone}: {$otp['otp']}");



            return response()->json([
                'message' => 'Account created',
                'user' => $userData
            ]);
        } catch (\Throwable $th) {
           return response()->json(["message" => $th->getMessage()], 400);
        }
    }

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




    public function verifyPhone(UserService $userService, UpdateUserProfileUseCase $updateProfileUseCase)
    {
        $validated = request()->validate([
            'user_id' => 'required|string',
            'otp_code'     => 'required|string',
        ]);


        try {


     
            $result = app(VerifyOtpUseCase::class)->execute(
                 new VerifyOtpCommand(
                    userId: $validated['user_id'],
                    otp: $validated['otp_code']
                )
            );

                        info($validated);

             $updateCommande = new UpdateUserProfileCommand(
                id: $result ['user_id'],
                phoneVerifiedAt: CarbonImmutable::now(),
                otpCode: null,
                otpExpiresAt: null
            );

            $userData = $updateProfileUseCase->execute($updateCommande);

            $token = $userService->generatPassportToken($validated['user_id']); //

            return response()->json([
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => $userData,
            ]);
        } catch (\Throwable $th) {
           return response()->json(["message" => $th->getMessage()], 400);
        }
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