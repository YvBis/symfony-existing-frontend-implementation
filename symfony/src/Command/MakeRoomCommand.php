<?php

namespace App\Command;

use App\Dto\UserRegistrationDto;
use App\Entity\Room;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:make-room',
    description: 'Makes room through CLI',
)]
class MakeRoomCommand extends Command
{
    private Generator $faker;

    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
        $this->faker = Factory::create();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Room name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name') ?? $this->generateName();
        $room = (new Room())->setName($name);
        $this->em->persist($room);
        $this->em->flush();

        $io->success(sprintf('Room with name %s created!', $name));

        return Command::SUCCESS;
    }

    private function generateName(): string
    {
        return $this->faker->words(nb: 5, asText: true);
    }

    private function renderViolations(SymfonyStyle $io, iterable $violations): void
    {
        $table = $io->createTable();
        $table->setHeaders(['Property', 'Property value', 'Message']);

        foreach ($violations as $violation) {
            $table->addRow([$violation->getPropertyPath(), $violation->getInvalidValue(), $violation->getMessage()]);
        }

        $table->render();
    }
}
