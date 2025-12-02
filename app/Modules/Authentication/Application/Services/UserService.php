<?php

namespace App\Modules\Authentication\Application\Services;

use App\Modules\Authentication\Application\Exceptions\CannotUpdateUserException;
use App\Modules\Authentication\Application\Services\HashingService;
use App\Modules\Authentication\Application\V1\Data\UserData;
use App\Modules\Authentication\Domain\Entities\UserEntity;
use App\Modules\Authentication\Domain\Enums\UserStatus;
use App\Modules\Authentication\Domain\ValueObjects\Email as ValueObjectsEmail;
use App\Modules\Authentication\Domain\ValueObjects\PhoneNumber;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use App\Modules\Authentication\Infrastructure\Repositories\EloquentUserRepository;
use App\Modules\User\Domain\Events\UserRegisteredEvent;
use App\Modules\User\Domain\Exceptions\UserNotFoundException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;


class UserService
{
    public function __construct(
        private EloquentUserRepository $eloquentUserRepository,
        private HashingService $hashingService,
    ) {}



     public function findByPhone(string $phone): ?UserData
     {
        $userEntity = $this->eloquentUserRepository->findByPhone(new PhoneNumber($phone));
        return UserData::fromEntity($userEntity);
     }


     public function findById(string $id): ?UserData
     {
        $userEntity = $this->eloquentUserRepository->findById((new Id($id))->value());
        return UserData::fromEntity($userEntity);
     }

     public function generatPassportToken(string $id): string {
        return $this->eloquentUserRepository->generatPassportToken($id);
     }
     public function deleteTokens(string $phone) {
        return $this->eloquentUserRepository->deleteTokens(new PhoneNumber($phone));

        
     }

    public function update(
        string $id,
        ?string $email = null,
        ?string $fullname = null,
        ?string $phone = null,
        ?string $phoneVerifiedAt = null,
        ?string $status = null,
        ?string $password = null,
        ?int $otpCode = null,
        ?string $otpExpiresAt = null
    ): UserData {


        try {

            DB::beginTransaction();

            // Récupérer l'utilisateur actuel 
            $userEntity = $this->eloquentUserRepository->findById((new Id($id))->value());
            if (!$userEntity) throw new UserNotFoundException();

            // Préparer les nouvelles valeurs
            $emailVO = $email ? new ValueObjectsEmail($email) : null;
            $phoneVO = $phone ? new PhoneNumber($phone) : null;
            $hashedPassword = $password ? $this->hashingService->hash($password) : null;
            $phoneVerifiedAt = CarbonImmutable::parse($phoneVerifiedAt);
            $otpExpiresAt = CarbonImmutable::parse($otpExpiresAt);

            $status = $status ? UserStatus::from($status): $userEntity->getStatus();

            // Mettre à jour l'utilisateur
            $userEntity->update(
                email: $emailVO,
                fullname: $fullname,
                phone: $phoneVO,
                status: $status,
                phoneVerifiedAt: $phoneVerifiedAt,
                hashedPassword: $hashedPassword,
                otpCode: $otpCode,
                otpExpiresAt: $otpExpiresAt,
            );

            // Persister les modifications
            $this->eloquentUserRepository->save($userEntity);

            // 5. Emit event
            // event(new UserUpdatedEvent($user));

            DB::commit();

            return UserData::fromEntity($userEntity);
        } catch (\Throwable $th) {
             DB::rollBack();
            // Log the error
            Log::error("Failed to update user: {$th->getMessage()}", [
                'user_id' => (new Id($id))->value(),
            ]);

            // Transform infrastructure errors → domain-safe exception
            throw new CannotUpdateUserException($th->getMessage());
        }
    }

    /**
     * Créer un compte utilisateur (sans handler)
     * Utilisé par : RegisterUserHandler, AdminCreateUserHandler, Jobs, Seeds, etc.
     */
    public function createUser(string $fullname, string $email, string $phone, string $password): UserData
    {
        $emailVO = new ValueObjectsEmail($email);
        $passwordHashed = $this->hashingService->hash($password);
        $id = Id::generate();
        $phoneVO = new PhoneNumber($phone);

        $userEntity = UserEntity::register(
            id: $id,
            fullname: $fullname,
            phone: $phoneVO,
            phoneVerifiedAt: null,
            email: $emailVO,
            status: UserStatus::ACTIVE,
            hashedPassword: $passwordHashed
        );

        // Persister
        $this->eloquentUserRepository->save($userEntity);

        // Émettre un événement de domaine
        // Event::dispatch(new UserRegisteredEvent($user));

        return UserData::fromEntity($userEntity);
    }

    /**
     * Changer le mot de passe
     */
    // public function changePassword(User $user, string $newPassword): User
    // {
    //     $passwordVO = Password::fromPlainText($newPassword);
    //     $user->changePassword($passwordVO);

    //     $this->eloquentUserRepository->save($user);

    //     return $user;
    // }

    /**
 * Trouver un utilisateur
 */
    // public function findByEmail(string $email): ?User
    // {
    //     return $this->eloquentUserRepository->findByEmail(new Email($email));
    // }

    /**
 * Mise à jour simple du profil
 */
    // public function updateProfile(User $user, string $name): User
    // {
    //     $user->updateProfile($name);

    //     $this->eloquentUserRepository->save($user);

    //     return $user;
    // }
}
