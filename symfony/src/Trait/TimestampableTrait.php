<?php

declare(strict_types=1);

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks()]
trait TimestampableTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    private \DateTimeInterface $updatedAt;

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = clone $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
