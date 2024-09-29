<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Methods;
use App\Repository\OutboxRepository;
use App\Trait\TimestampableEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutboxRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Outbox
{
    use TimestampableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::STRING, nullable: false, enumType: Methods::class)]
    private Methods $method = Methods::PUBLISH;

    /**
     * @var array<mixed>
     */
    #[ORM\Column(type: Types::JSON, nullable: false)]
    private array $payload = [];

    #[ORM\Column(type: Types::BIGINT)]
    private int $partition = 0;

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

    /**
     * @return mixed[]
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param mixed[] $payload
     */
    public function setPayload(array $payload): Outbox
    {
        $this->payload = $payload;

        return $this;
    }

    public function getPartit(): int
    {
        return $this->partition;
    }

    public function setPartit(int $partition): Outbox
    {
        $this->partition = $partition;

        return $this;
    }
}
