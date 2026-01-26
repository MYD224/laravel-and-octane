<?php

namespace App\Modules\Authentication\Interface\Http\Controllers\Api\V1;

use App\Modules\Navigation\Application\V1\UseCases\GetNavigationTreeUseCase;
use App\Core\Application\UseCases\GenerateTokenUseCase;
use App\Core\Interface\Controllers\BaseController;
use App\Modules\Authentication\Application\Services\HashingService;
// use App\Modules\Authentication\Application\Services\UserService;
use App\Modules\Authentication\Application\Services\UserService;
use App\Modules\Authentication\Application\V1\Commands\GenerateOtpCommand;
use App\Modules\Authentication\Application\V1\Commands\LogoutCommand;
use App\Modules\Authentication\Application\V1\Commands\RegisterUserCommand;
use App\Modules\Authentication\Application\V1\Commands\UpdateUserProfileCommand;
use App\Modules\Authentication\Application\V1\Commands\VerifyOtpCommand;
use App\Modules\Authentication\Application\V1\Data\UserData;
use App\Modules\Authentication\Application\V1\Handlers\GenerateOtpHandler;
use App\Modules\Authentication\Application\V1\UseCases\GenerateOtpUseCase;
use App\Modules\Authentication\Application\V1\UseCases\LogoutUseCase;
use App\Modules\Authentication\Application\V1\UseCases\RegisterUserUseCase;
use App\Modules\Authentication\Application\V1\UseCases\UpdateUserProfileUseCase;
use App\Modules\Authentication\Application\V1\UseCases\VerifyOtpUseCase;
use App\Modules\Authentication\Application\V1\UseCases\LoginUser;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Modules\Authentication\Domain\ValueObjects\Email;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Domain\ValueObjects\PhoneNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;



class AuthController extends BaseController
{
    public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly HashingService $hashingService) {}


    public function login(
        Request $request,
        UserRepositoryInterface $userRepository,
        LoginUser $loginUseCase,
        GenerateTokenUseCase $generateTokenUseCase,
        GetNavigationTreeUseCase $getNavigationTreeUseCase
    ) {

        $validator = Validator::make($request->all(), [
            'phone'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $userEntity = $userRepository->findByPhone($request->phone);

        if (!$userEntity) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!$userEntity->getPhoneVerifiedAt()) {
            return response()->json(['message' => 'Please verify your phone first.'], 403);
        }

        // Check user status
        // if ($userEntity->getStatus() !== UserStatus::ACTIVE) {
        //     throw new InvalidStatusException("User account is not active.");
        // }

        // Revoke old tokens (optional, to ensure one active session)
        $userRepository->deleteTokens($userEntity->getPhoneNumber());

        $isLoggedIn = $loginUseCase->execute($request->phone, $request->password);
        $otp = null;
        $message = '';
        $userMenus = [];
        if (!$isLoggedIn) {
            return response()->json([
                'message' => 'Invalid credentials',
                'access_token' => null,
                'user' => null,
                'user_menus' => [],
                'otp_expires_at' => null,
            ], 422);
        }
        if ($userEntity->getIsSendOtp()) {
            $otp = app(GenerateOtpUseCase::class)->execute(
                new GenerateOtpCommand($userEntity->getId())
            );
            // Send via SMS gateway (or fake for dev)
            // SmsService::send($request->phone, "Your OTP is: {$otp}");
            // TODO: send OTP via SMS or WhatsApp (for now just log it)
            info("OTP for {$userEntity->getPhone()}: {$otp['otp']}");
            $message = 'user verified successfully. Please verify your otp.';
        } else {
            $token = $generateTokenUseCase->execute($userEntity->getId()); //
            //get user permissions and menus
            $userMenus = $getNavigationTreeUseCase->execute(null, $userEntity->getId());
            $userRepository->saveConnexion($userEntity->getId(), null);
            $message = 'User logged in successfully';
        }

        return response()->json([
            'message' => $message,
            'access_token' => isset($token) ? $token : null,
            'user' => UserData::fromEntity($userEntity),
            'user_menus' => $userMenus,
            'otp_expires_at' => isset($otp) ? $otp['expires_at'] : null,
        ]);
    }

    public function register(
        Request $request,
        RegisterUserUseCase $registerUserUseCase,
    ) {


        try {

            $validator = Validator::make($request->all(), [
                // 'fullname'     => 'required|string|max:255',
                'firstnames'     => 'required|string|max:255',
                'lastname'     => 'required|string|max:255',
                'gender' => 'require|string|max:8',
                'phone'    => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $command = new RegisterUserCommand(
                // fullname: $request->fullname,
                firstnames: $request->firstnames,
                lastname: $request->lastname,
                gender: $request->gender,
                phone: $request->phone,
                email: $request->email ?? null,
                password: $request->password
            );


            $userEntity = $registerUserUseCase->execute($command);

            // Generate OTP
            $otp = app(GenerateOtpUseCase::class)->execute(
                new GenerateOtpCommand($userEntity->getId(), 30000)
            );




            // Send via SMS gateway (or fake for dev)
            // SmsService::send($request->phone, "Your OTP is: {$otp}");
            // TODO: send OTP via SMS or WhatsApp (for now just log it)
            info("OTP for {$userEntity->getPhone()}: {$otp['otp']}");



            return response()->json([
                'message' => 'Account created',
                'user' => UserData::fromEntity($userEntity),
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




    public function verifyPhone(
        UpdateUserProfileUseCase $updateProfileUseCase,
        GenerateTokenUseCase $generateTokenUseCase,
        GetNavigationTreeUseCase $getNavigationTreeUseCase,
        UserRepositoryInterface $userRepository
    ) {
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
            $updateCommande = new UpdateUserProfileCommand(
                id: $result['user_id'],
                phoneVerifiedAt: CarbonImmutable::now(),
            );
            $message = $result['message'] ?? '';
            $userData = $updateProfileUseCase->execute($updateCommande);
            $token = $generateTokenUseCase->execute($validated['user_id']); //
            //get user permissions and menus
            $userMenus = $getNavigationTreeUseCase->execute(null, $validated['user_id']);
            $userRepository->saveConnexion($validated['user_id'], $validated['otp_code']);
            return response()->json([
                'message' => $message,
                'access_token' => $token,
                'user' => $userData,
                'user_menus' => $userMenus,
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


    /**
     * Rediriger vers le provider OAuth
     */
    public function redirect($provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)
            ->stateless()
            ->redirect();
    }

    /**
     * Gérer le callback du provider OAuth
     */
    public function callback($provider)
    {
        $this->validateProvider($provider);

        try {
            // Récupérer les informations de l'utilisateur depuis le provider
            $socialUser = Socialite::driver($provider)->stateless()->user();
            // Trouver ou créer l'utilisateur
            $user = $this->findOrCreateUser($socialUser, $provider);

            // Créer un token Passport
            $token = $this->userRepository->generatPassportToken($user->getId());
            $id = $user->getId();
            $name = $user->getFullname();
            $email = $user->getEmail();
            // Rediriger vers le frontend avec le token
            $redirectUrl = sprintf(
                '%s/auth/callback?token=%s&user=%s',
                config('app.frontend_url'),
                $token,
                urlencode(json_encode([
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    // 'user' => $user,
                    // 'avatar' => $user->avatar,
                ]))
            );

            return redirect()->away($redirectUrl);
        } catch (\Exception $e) {
            // En cas d'erreur, rediriger vers le frontend avec l'erreur
            $errorUrl = sprintf(
                '%s/auth/callback?error=%s',
                config('app.frontend_url'),
                urlencode('Échec de l\'authentification')
            );

            return redirect()->away($errorUrl);
        }
    }

    /**
     * Trouver ou créer un utilisateur basé sur les infos du provider
     */
    protected function findOrCreateUser($socialUser, $provider)
    {
        // Chercher un utilisateur avec ce provider et provider_id
        $user = $this->userRepository->findByAuthProviderAndProviderId($provider, $socialUser->getId());

        if ($user) {
            return $user;
        }
        $email = new Email($socialUser->getEmail());
        // Vérifier si un utilisateur existe avec cet email
        $existingUser = $this->userRepository->findByEmail($email->value());
        if ($existingUser) {
            // Lier le compte existant au provider social
            $user = $this->userRepository->updateUserAfterSocialRegistration(
                $existingUser->getId(),
                $provider,
                $socialUser->getId(),
                $existingUser->getEmail(),
                $existingUser->getFullname(),
                $existingUser->getHashedPassword(),
                now()
            );

            return $user;
        }
        $hashedPassword = $this->hashingService->hash(Str::random(24));
        // Créer un nouvel utilisateur
        $user = $this->userRepository->updateUserAfterSocialRegistration(
            Id::generate()->value(),
            $provider,
            $socialUser->getId(),
            $socialUser->getEmail(),
            $socialUser->getName(),
            $hashedPassword,
            now()
        );
        return $user;
    }

    /**
     * Valider le provider
     */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }
    }
}
