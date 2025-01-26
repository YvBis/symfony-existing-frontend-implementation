<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Attribute\CheckRequestAttributeTrue;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

readonly class CheckCsrfSubscriber implements EventSubscriberInterface
{
    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $csrfAttributes = $event->getAttributes(CheckRequestAttributeTrue::class);
        $requestAttributes = $event->getRequest()->attributes;
        /** @var CheckRequestAttributeTrue $csrfAttribute */
        foreach ($csrfAttributes as $csrfAttribute) {
            $attributeId = $csrfAttribute->id;
            if (!$requestAttributes->has($attributeId) || $requestAttributes->get($attributeId) !== true) {
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
