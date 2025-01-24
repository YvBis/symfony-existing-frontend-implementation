<?php

declare(strict_types=1);

namespace App\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\HasLifecycleCallbacks()]
trait BumpableEntityTrait
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[SerializedName('bumped_at')]
    private ?\DateTimeInterface $bumpedAt = null;

    public function getBumpedAt(): ?\DateTimeInterface
    {
        return $this->bumpedAt;
    }

    public function setBumpedAt(\DateTimeInterface $bumpedAt): void
    {
        $this->bumpedAt = $bumpedAt;
    }

    #[ORM\PrePersist]
    public function updateBumpedAt(): void
    {
        $this->bumpedAt = new \DateTimeImmutable();
    }
}
