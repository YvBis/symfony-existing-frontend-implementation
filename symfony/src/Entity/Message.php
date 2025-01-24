<?php

namespace App\Entity;

use App\Interface\TimestampableEntityInterface;
use App\Repository\MessageRepository;
use App\Trait\TimestampableEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Message implements TimestampableEntityInterface
{
    use TimestampableEntityTrait;

    public const API_LIST_GROUP = 'message:list';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: [Room::API_LIST_GROUP, self::API_LIST_GROUP])]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'relation_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\OrderBy(['bumpedAt' => 'DESC'])]
    #[SerializedName('room')]
    #[Groups(groups: [self::API_LIST_GROUP])]
    private ?Room $relation;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[Groups(groups: [Room::API_LIST_GROUP, self::API_LIST_GROUP])]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(groups: [Room::API_LIST_GROUP, self::API_LIST_GROUP])]
    private string $content = '';

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    #[SerializedName('created_at')]
    #[Groups(groups: [Room::API_LIST_GROUP, self::API_LIST_GROUP])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    #[SerializedName('updated_at')]
    private ?\DateTimeInterface $updatedAt = null;

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
