<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/token')]
class TokenController extends AbstractController
{
    #[Route('/connection', name: 'api_connection_token')]
    public function getConnectionToken(): JsonResponse
    {
        return $this->json('connection', Response::HTTP_OK);
    }

    #[Route('/subscription', name: 'api_subscription_token')]
    public function getSubscriptionToken(): JsonResponse
    {
        return $this->json('subscription', Response::HTTP_OK);
    }
}
