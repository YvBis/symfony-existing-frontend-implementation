<?php

namespace App\Controller\Api;

use App\Dto\UserRegistrationDto;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    #[Route('registerUser', name: 'api_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] UserRegistrationDto $registrationDto,
    ): JsonResponse {
        $user = $this->userService->registerUser($registrationDto);

        return $this->json([
            'message' => 'User created successfully',
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ], Response::HTTP_CREATED);
    }

    #[Route('login', name: 'api_login', methods: ['POST'])]
    public function login(
        #[CurrentUser] ?User $user
    ): JsonResponse {
        if (null === $user) {
            return $this->json(['detail' => 'invalid credentials'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            ],
        ], Response::HTTP_OK);
    }
}
