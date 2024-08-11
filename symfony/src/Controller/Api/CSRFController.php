<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CSRFController extends AbstractController
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfManager
    ) {
    }

    #[Route('csrf', name: 'api_csrf')]
    public function getCsrf(): JsonResponse
    {
        $token = $this->csrfManager->getToken('');

        return $this->json(
            data: [],
            headers: ['X-CSRFToken' => $token->getValue()],
        );
    }
}
