<?php

namespace App\Tests\Traits;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait CsrfTokenStubbedTrait
{
    public function stubCsrfTokenManager(): void
    {
        $csrfTokenManagerMock = $this->createConfiguredMock(
            originalClassName: CsrfTokenManagerInterface::class,
            configuration: [
                'isTokenValid' => true,
            ]
        );
        static::getContainer()->set(CsrfTokenManagerInterface::class, $csrfTokenManagerMock);
    }

    public function makeClient(): KernelBrowser
    {
        $client = static::createClient();
        $csrfTokenManagerMock = $this->createConfiguredMock(
            originalClassName: CsrfTokenManagerInterface::class,
            configuration: [
                'isTokenValid' => true,
            ]
        );
        static::getContainer()->set(CsrfTokenManagerInterface::class, $csrfTokenManagerMock);

        return $client;
    }
}
