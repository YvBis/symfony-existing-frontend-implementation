<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Methods;
use App\Repository\OutboxRepository;
use App\Trait\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutboxRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Outbox
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(enumType: Methods::class)]
    private Methods $method = Methods::PUBLISH;

    #[ORM\Column]
    private array $payload = [];

    #[ORM\Column(type: Types::BIGINT)]
    private string $partit;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Outbox
    {
        $this->id = $id;

        return $this;
    }

    public function getMethod(): Methods
    {
        return $this->method;
    }

    public function setMethod(Methods $method): Outbox
    {
        $this->method = $method;

        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): Outbox
    {
        $this->payload = $payload;

        return $this;
    }

    public function getPartit(): string
    {
        return $this->partit;
    }

    public function setPartit(string $partit): Outbox
    {
        $this->partit = $partit;

        return $this;
    }
}
