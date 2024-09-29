<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RoomRepository;
use App\Trait\TimestampableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Room
{
    use TimestampableEntityTrait;

    public const API_LIST_GROUP = 'room:list';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: [self::API_LIST_GROUP, Message::API_LIST_GROUP])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Groups(groups: [self::API_LIST_GROUP, Message::API_LIST_GROUP])]
    private string $name;
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[Groups(groups: [self::API_LIST_GROUP, Message::API_LIST_GROUP])]
    #[ORM\Version]
    private int $version = 1;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(groups: [self::API_LIST_GROUP, Message::API_LIST_GROUP])]
    #[SerializedName('bumped_at')]
    private ?\DateTimeInterface $bumpedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    #[SerializedName('created_at')]
    #[Groups(groups: self::API_LIST_GROUP)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    #[SerializedName('updated_at')]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'relation', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'room_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $messages;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'rooms')]
    #[ORM\JoinTable(name: 'room_members')]
    #[ORM\JoinColumn(name: 'room_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'member_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $members;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'last_message_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(groups: self::API_LIST_GROUP)]
    #[SerializedName('last_message')]
    private ?Message $lastMessage = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Room
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getBumpedAt(): ?\DateTimeInterface
    {
        return $this->bumpedAt;
    }

    public function setBumpedAt(?\DateTimeInterface $bumpedAt): static
    {
        $this->bumpedAt = $bumpedAt;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setRelation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getRelation() === $this) {
                $message->setRelation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(User $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): static
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    #[Groups(groups: self::API_LIST_GROUP)]
    #[SerializedName('member_count')]
    public function getMembersCount(): int
    {
        return $this->members->count();
    }
}
