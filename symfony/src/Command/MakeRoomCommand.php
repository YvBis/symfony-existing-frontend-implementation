<?php

namespace App\Command;

use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:make-room',
    description: 'Makes room through CLI',
)]
class MakeRoomCommand extends Command
{
    private Generator $faker;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        $this->faker = Factory::create();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $this->askForName($io);
        $room = (new Room())->setName($name);
        $this->em->persist($room);
        $this->em->flush();

        $io->success(sprintf('Room with name %s created!', $name));

        return Command::SUCCESS;
    }

    private function askForName(SymfonyStyle $io): string
    {
        $name = $io->ask('Please enter name or leave blank if you want name to be generated.');
        if (null === $name || '' === $name) {
            $name = $this->generateName();
            $io->info(\sprintf('Autogenerated name: %s.', $name));
        }

        return $name;
    }

    private function generateName(): string
    {
        return $this->faker->words(nb: 5, asText: true); // @phpstan-ignore-line
    }
}
