<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use App\Trait\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $relation;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $content = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelation(): ?Room
    {
        return $this->relation;
    }

    public function setRelation(?Room $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
