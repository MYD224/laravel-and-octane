<?php
namespace App\Modules\Authentication\Application\V1\UseCases;

class RegisterUser
{
    public function __construct(private UserRepository $repository) {}

    public function execute(string $email, string $password): User
    {
        // hash password
        $hash = bcrypt($password);

        // create domain entity
        $user = new User(id: uniqid(), email: $email, passwordHash: $hash, isActive: true);

        // save using repository (infrastructure)
        $this->repository->save($user);

        return $user;
    }
}
