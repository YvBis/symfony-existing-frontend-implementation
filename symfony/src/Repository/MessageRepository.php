<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return array<Message>
     */
    public function getMessagesByRoom(Room $room): array
    {
        return $this->createQueryBuilder('message')
            ->join('message.relation', 'room')
            ->leftJoin('message.user', 'user')
            ->andWhere('room = :room')
            ->setParameter('room', $room)
            ->orderBy('message.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
