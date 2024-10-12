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

        $io = new SymfonyStyle($input, $output);

        if ($this->entityManager->getRepository(User::class)->findOneBy(['username' => 'mave']) !== null) {
            $io->error("Nejakej uzivatel uz existuje.");
            return Command::FAILURE;
        }

        $user = new User(
            'mave',
            'plihal.marek@seznam.cz',
            'Marek',
            'Plíhal'
        );
        $user->isVerified(true);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('$2y$13$vqVJHSfRebzyXWx7tan9F.Gzx/33By3qF3lS4cqd5ux4uvw7yv85u');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success("Byly nahrány úvodní data.");

        return Command::SUCCESS;
    }
}