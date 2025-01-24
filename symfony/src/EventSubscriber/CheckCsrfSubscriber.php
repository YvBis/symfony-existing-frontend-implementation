<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\CheckCsrf;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

readonly class CheckCsrfSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $csrfAttributes = $event->getAttributes(CheckCsrf::class);
        $request = $event->getRequest();
        /** @var CheckCsrf $csrfAttribute */
        foreach ($csrfAttributes as $csrfAttribute) {
            $tokenId = $csrfAttribute->id;
            $tokenHeaderKey = $csrfAttribute->tokenHeaderKey;
            $tokenCookieKey = $csrfAttribute->tokenCookieKey;
            $csrfTokenInHeader = $request->headers->get($tokenHeaderKey);
            $csrfTokenInCookie = $request->cookies->get($tokenCookieKey);
            $csrfTokenToCheck = $csrfTokenInHeader ?? $csrfTokenInCookie;

            if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($tokenId, $csrfTokenToCheck))) {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
        ];
    }
}
