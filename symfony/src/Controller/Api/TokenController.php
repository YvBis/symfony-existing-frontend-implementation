<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Attribute\CheckCsrf;
use App\Dto\ChannelSubscriptionDto;
use App\Enum\ChannelTemplates;
use App\Enum\CsrfTokenConstant;
use App\Service\TokenGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/token')]
#[CheckCsrf(id: CsrfTokenConstant::API->value, tokenKey: CsrfTokenConstant::TOKEN_KEY->value)]
class TokenController extends AbstractController
{
    public function __construct(
        private readonly TokenGeneratorService $tokenGeneratorService,
    ) {
    }

    #[Route('/connection', name: 'api_connection_token')]
    public function getConnectionToken(): JsonResponse
    {
        $user = $this->getCurrentUserOrFail();
        $token = $this->tokenGeneratorService->getConnectionToken($user->getUserIdentifier());

        return $this->json(['token' => $token], Response::HTTP_OK);
    }

    #[Route('/subscription', name: 'api_subscription_token')]
    public function getSubscriptionToken(
        #[MapRequestPayload] ChannelSubscriptionDto $channelSubscriptionDto
    ): JsonResponse {
        $user = $this->getCurrentUserOrFail();
        if ($channelSubscriptionDto->channelName !== sprintf(ChannelTemplates::PERSONAL->value, $user->getUserIdentifier())) {
            return $this->json(['detail' => 'permission denied'], Response::HTTP_FORBIDDEN);
        }

        $token = $this->tokenGeneratorService->getSubscriptionToken(
            $user->getUserIdentifier(),
            $channelSubscriptionDto->channelName
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
