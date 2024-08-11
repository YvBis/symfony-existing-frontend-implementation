<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\UserRegistrationDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function registerUser(UserRegistrationDto $registrationDto): User
    {
        $user = new User();
        $user->setUsername($registrationDto->username);
        $user->setEmail($registrationDto->email);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $registrationDto->plainPassword
            )
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
