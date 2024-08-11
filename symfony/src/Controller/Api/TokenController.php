<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\ChannelSubscriptionDto;
use App\Enum\JwtAlgorithms;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/token')]
class TokenController extends AbstractController
{
    public function __construct(
        #[Autowire('%env(CENTRIFUGO_TOKEN_SECRET)%')] private readonly string $tokenSecret,
        #[Autowire('%env(CENTRIFUGO_TOKEN_TTL)%')] private readonly int $tokenTtl,
    )
    {}

    #[Route('/connection', name: 'api_connection_token')]
    public function getConnectionToken(): JsonResponse
    {
        $user = $this->getCurrentUserOrFail();
        $payload = [
            'sub' => $user->getUserIdentifier(),
            'exp' => time() + $this->tokenTtl,
        ];
        $token = JWT::encode($payload, $this->tokenSecret, JwtAlgorithms::HS256->value);

        return $this->json(['token' => $token], Response::HTTP_OK);
    }

    #[Route('/subscription', name: 'api_subscription_token')]
    public function getSubscriptionToken(
        #[MapRequestPayload] ChannelSubscriptionDto $channelSubscriptionDto
    ): JsonResponse {
        $user = $this->getCurrentUserOrFail();
        $payload = [
            'sub' => $user->getUserIdentifier(),
            'exp' => time() + $this->tokenTtl,
            'channel' => $channelSubscriptionDto->channelName,
        ];
        $token = JWT::encode($payload, $this->tokenSecret, JwtAlgorithms::HS256->value);

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
