<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        [$user1, $user2, $user3] = $this->generateUsers($manager);
        $user1->setCreatedAt(new \DateTimeImmutable('-1 month'));
        $user2->setCreatedAt(new \DateTimeImmutable('-2 months'));
        $user3->setCreatedAt(new \DateTimeImmutable('-3 months'));
        $manager->flush();

        [$room1, $room2, $room3] = $this->generateRooms($manager);
        $room1->setCreatedAt(new \DateTimeImmutable('-4 month'));
        $room2->setCreatedAt(new \DateTimeImmutable('-5 months'));
        $room3->setCreatedAt(new \DateTimeImmutable('-6 months'));
        $room1->addMember($user1)->addMember($user2);
        $room2->addMember($user1)->addMember($user2)->addMember($user3);
        $room3->addMember($user1)->addMember($user3);
        $manager->flush();

        $message1 = (new Message())->setUser($user1)->setContent('message 1')->setRelation($room1);
        $manager->persist($message1);
        $message1->setCreatedAt($room1->getCreatedAt()->modify('+3 hour'));

        $message2 = (new Message())->setUser($user1)->setContent('message 2')->setRelation($room1);
        $manager->persist($message2);
        $message2->setCreatedAt($room1->getCreatedAt()->modify('+4 hour'));

        $message3 = (new Message())->setUser($user1)->setContent('message 3')->setRelation($room1);
        $manager->persist($message3);
        $message3->setCreatedAt($room1->getCreatedAt()->modify('+5 hour'));

        $message4 = (new Message())->setUser($user2)->setContent('message 4')->setRelation($room1);
        $manager->persist($message4);
        $message4->setCreatedAt($room1->getCreatedAt()->modify('+6 hour'));

        $message5 = (new Message())->setUser($user2)->setContent('message 5')->setRelation($room1);
        $manager->persist($message5);
        $message5->setCreatedAt($room1->getCreatedAt()->modify('+7 hour'));
        $room1->setLastMessage($message5);
        $room1->setBumpedAt($message5->getCreatedAt());

        $message6 = (new Message())->setUser($user2)->setContent('message 6')->setRelation($room2);
        $manager->persist($message6);
        $message6->setCreatedAt($room2->getCreatedAt()->modify('+3 hour'));

        $message7 = (new Message())->setUser($user2)->setContent('message 7')->setRelation($room2);
        $manager->persist($message7);
        $message7->setCreatedAt($room2->getCreatedAt()->modify('+4 hour'));

        $message8 = (new Message())->setUser($user1)->setContent('message 8')->setRelation($room2);
        $manager->persist($message8);
        $message8->setCreatedAt($room2->getCreatedAt()->modify('+5 hour'));

        $message9 = (new Message())->setUser($user3)->setContent('message 9')->setRelation($room2);
        $manager->persist($message9);
        $message9->setCreatedAt($room2->getCreatedAt()->modify('+6 hour'));
        $room2->setLastMessage($message9);
        $room2->setBumpedAt($message9->getCreatedAt());

        $message10 = (new Message())->setUser($user3)->setContent('message 10')->setRelation($room3);
        $manager->persist($message10);
        $message10->setCreatedAt($room3->getCreatedAt()->modify('+3 hour'));

        $message11 = (new Message())->setUser($user3)->setContent('message 11')->setRelation($room3);
        $manager->persist($message11);
        $message11->setCreatedAt($room3->getCreatedAt()->modify('+4 hour'));

        $message12 = (new Message())->setUser($user3)->setContent('message 12')->setRelation($room3);
        $manager->persist($message12);
        $message12->setCreatedAt($room3->getCreatedAt()->modify('+5 hour'));

        $message13 = (new Message())->setUser($user1)->setContent('message 13')->setRelation($room3);
        $manager->persist($message13);
        $message13->setCreatedAt($room3->getCreatedAt()->modify('+6 hour'));

        $message14 = (new Message())->setUser($user1)->setContent('message 14')->setRelation($room3);
        $manager->persist($message14);
        $message14->setCreatedAt($room3->getCreatedAt()->modify('+7 hour'));
        $room3->setLastMessage($message14);
        $room3->setBumpedAt($message14->getCreatedAt());

        $manager->flush();
    }

    /**
     * @return array<int, User>
     */
    public function generateUsers(ObjectManager $manager): array
    {
        $result = [];
        foreach (range(1, 3) as $i) {
            $user = new User();
            $user->setUsername('username'.$i)
                ->setEmail(sprintf('email%s@example.com', $i))
                ->setPassword($this->hasher->hashPassword($user, '4321'));
            $result[] = $user;
            $manager->persist($user);
        }

        return $result;
    }

    /**
     * @return array<int, Room>
     */
    public function generateRooms(ObjectManager $manager): array
    {
        $result = [];
        foreach (range(1, 3) as $i) {
            $room = new Room();
            $room->setName(sprintf('Room%s', $i))->setVersion(1);
            $result[] = $room;

            $manager->persist($room);
        }

        return $result;
    }
}
