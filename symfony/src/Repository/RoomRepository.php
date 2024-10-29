<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * @param User $user
     * @return array<Room>
     */
    public function getRoomsByUser(User $user): array
    {
        return $this->createQueryBuilder('room')
            ->join('room.members', 'members')
            ->leftJoin('room.lastMessage', 'last_message')
            ->where('members = :user')
            ->setParameter('user', $user)
            ->orderBy('room.bumpedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getRoomForUserById(Room $room, User $user): ?Room
    {
        return $this->createQueryBuilder('room')
            ->join('room.members', 'members')
            ->leftJoin('room.lastMessage', 'last_message')
            ->where('members = :user')
            ->andWhere('room = :room')
            ->setParameter('user', $user)
            ->setParameter('room', $room)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<array{id: int, name: string, createdAt: \DateTimeImmutable, is_member: 0|1}>
     */
    public function getRoomsByUserForSearch(User $user): array
    {
        return $this->createQueryBuilder('room')
            ->select('room.id, room.name, room.createdAt')
            ->addSelect('MAX(CASE WHEN room_member.id = :user_id THEN true ELSE false END) AS is_member')
            ->leftJoin('room.members', 'room_member')
            ->setParameter('user_id', $user->getId())
            ->groupBy('room.id')
            ->getQuery()
            ->getResult();
    }
}
