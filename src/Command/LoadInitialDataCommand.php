<?php

namespace App\Command;

use App\Entity\Classroom;
use App\Entity\Test;
use App\Entity\TestUser;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'LoadInitialData',
    description: 'Nahraje uvodni data do db',
)]
class LoadInitialDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // zde se muzou nahrat puvodni data kdyby nejake byli potreba, napriklad administrator atd.

        // priklad:

        // $io = new SymfonyStyle($input, $output);

        // $user = new User();
        // $user->setName("asa");
        // //....
        // $this->entityManager->persist($user);
        // $this->entityManager->flush();

        // $io->success("Byly nahrány úvodní data.");

        // return Command::SUCCESS;
    }
}