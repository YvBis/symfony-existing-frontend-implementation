<?php

namespace App\Controller\Chat;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChatTestController extends AbstractController
{
    #[Route('/test', name: 'app_chat_test')]
    public function index(): Response
    {
        return $this->json('coco chanel');
    }
}
