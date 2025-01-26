<?php

namespace App\EventSubscriber;

use App\Enum\CsrfTokenConstant;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

readonly class CsrfTokenSubscriber implements EventSubscriberInterface
{
    public function __construct(private CsrfTokenManagerInterface $csrfTokenManager)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $request->attributes->set(CsrfTokenConstant::ATTRIBUTE->value, false);
        $csrfTokenInHeader = (string) $request->headers->get(CsrfTokenConstant::TOKEN_KEY->value);
        if ($this->csrfTokenManager->isTokenValid(new CsrfToken(CsrfTokenConstant::API->value, $csrfTokenInHeader))) {
            $request->attributes->set(CsrfTokenConstant::ATTRIBUTE->value, true);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9],
        ];
    }
}
