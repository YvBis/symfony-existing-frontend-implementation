<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Enum\CsrfTokenConstant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CSRFController extends AbstractController
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfManager
    ) {
    }

    #[Route('csrf/', name: 'api_csrf')]
    public function getCsrf(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $session->start();

        $this->csrfManager->refreshToken(CsrfTokenConstant::API->value);
        $token = $this->csrfManager->getToken(CsrfTokenConstant::API->value);

        $session->save();

        return $this->json(
            data: Response::$statusTexts[Response::HTTP_OK],
            headers: ['X-CSRFToken' => $token->getValue()],
        );
    }
}
