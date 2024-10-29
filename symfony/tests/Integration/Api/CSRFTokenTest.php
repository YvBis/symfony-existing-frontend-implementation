<?php

declare(strict_types=1);

namespace App\Tests\Integration\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class CSRFTokenTest extends WebTestCase
{
    public function testGetCsrfToken(): void
    {
        $client = static::createClient();
        $client->jsonRequest(
            Request::METHOD_GET,
            '/api/csrf',
        );
        $csrfToken = $client->getResponse()->headers->all('X-CSRFToken');

        $this->assertResponseIsSuccessful();
        $this->assertIsArray($csrfToken);
        $this->assertNotEmpty($csrfToken);
        $this->assertCount(1, $csrfToken);
        $this->assertIsString($csrfToken[0]);
    }
}
