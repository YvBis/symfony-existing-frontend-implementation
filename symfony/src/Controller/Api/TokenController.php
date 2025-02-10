<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\ChannelSubscriptionDto;
use App\Service\TokenGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/token')]
class TokenController extends AbstractController
{
    public function __construct(
        private readonly TokenGeneratorService $tokenGeneratorService,
        private readonly string $centrifugoPersonalChannelPrefix,
    ) {
    }

    #[Route('/connection/', name: 'api_connection_token', methods: ['GET'])]
    public function getConnectionToken(): JsonResponse
    {
        $user = $this->getCurrentUserOrFail();
        $token = $this->tokenGeneratorService->getConnectionToken($user->getUserIdentifier());

        return $this->json(['token' => $token], Response::HTTP_OK);
    }

    #[Route('/subscription/', name: 'api_subscription_token', methods: ['GET'])]
    public function getSubscriptionToken(
        #[MapQueryString] ChannelSubscriptionDto $channelSubscriptionDto,
    ): JsonResponse {
        $user = $this->getCurrentUserOrFail();
        if ($channelSubscriptionDto->channel !== sprintf('%s:%s', $this->centrifugoPersonalChannelPrefix, $user->getUserIdentifier())) {
            return $this->json(['detail' => 'permission denied'], Response::HTTP_FORBIDDEN);
        }

        $token = $this->tokenGeneratorService->getSubscriptionToken(
            $user->getUserIdentifier(),
            $channelSubscriptionDto->channel
        );

        return $this->json(['token' => $token], Response::HTTP_OK);
    }

    private function getCurrentUserOrFail(): UserInterface
    {
        $user = $this->getUser();
        if (!$user) {
            $this->createAccessDeniedException();
        }

        return $user;
    }
}
