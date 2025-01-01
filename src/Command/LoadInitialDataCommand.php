<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Tag;
use DateTime;
use DateTimeZone;
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

        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => 'plihal.marek@seznam.cz']) !== null) {
            $io->error("Uživatel s daným emailem je už zde zaregistrován. Prosím, vyberte jiný email.");
            return Command::FAILURE;
        }

        $user = new User(
            'plihal.marek@seznam.cz',
            'Marek',
            'Plíhal'
        );

        $user->setVerified(true);
        $user->setPassword('$2y$13$vqVJHSfRebzyXWx7tan9F.Gzx/33By3qF3lS4cqd5ux4uvw7yv85u');
        $user->setLastVisit(new DateTime('now', new DateTimeZone('Europe/Prague')));

        $tag1 = new Tag("Diamantová technika", '/img/exhibitionPictures/diaTech.png');
        $tag2 = new Tag("Brusné kotouče", '/img/exhibitionPictures/brusKartace.png');
        $tag3 = new Tag("Katalog výrobků", '/img/exhibitionPictures/katalog.png');
        $tag4 = new Tag("Nářadí BAVARIA", '/img/exhibitionPictures/bavaria.png');
        $tag5 = new Tag("Plastiky a gravírace", '/img/exhibitionPictures/gravirace.png');
        $tag6 = new Tag("Žulové rotační výrobky", '/img/exhibitionPictures/zulove.png');
        $tag7 = new Tag("Zlato", '/img/exhibitionPictures/zlato.png');
        $tag8 = new Tag("Přísavky na kámen", '/img/exhibitionPictures/prisavky.png');
        $tag9 = new Tag("Chemie na kámen", '/img/exhibitionPictures/chemie.png');

        
        $this->entityManager->persist($tag1);
        $this->entityManager->persist($tag2);
        $this->entityManager->persist($tag3);
        $this->entityManager->persist($tag4);
        $this->entityManager->persist($tag5);
        $this->entityManager->persist($tag6);
        $this->entityManager->persist($tag7);
        $this->entityManager->persist($tag8);
        $this->entityManager->persist($tag9);

        $tag10 = new Tag("Ruční broušení", '/img/exhibitionPictures/ruc.png');
        $tag11 = new Tag("Diamantové řezací kotouče", '/img/exhibitionPictures/rez.png');
        $tag12 = new Tag("Kalibrovací frézy", '/img/exhibitionPictures/frezy.png');
        $tag13 = new Tag("Vrtací korunky", '/img/exhibitionPictures/kor.png');

        $tag1->addChildrenTag($tag10);
        $tag1->addChildrenTag($tag11);
        $tag1->addChildrenTag($tag12);
        $tag1->addChildrenTag($tag13);

        $this->entityManager->persist($tag10);
        $this->entityManager->persist($tag11);
        $this->entityManager->persist($tag12);
        $this->entityManager->persist($tag13);

        $product = new Product(
            'První produkt nevim',
            'diaTech/frezy/1.webp',
            true,
            12090
        );

        $product1 = new Product(
            'Druhž produkt nevim',
            'diaTech/frezy/2.webp',
            false,
            4090
        );

        $product2 = new Product(
            'treti produkt nevim',
            'diaTech/frezy/3.webp',
            true,
            33090
        );

        $tag3->addProduct($product);
        $tag3->addProduct($product1);
        $tag3->addProduct($product2);
        
        $this->entityManager->persist($product);
        $this->entityManager->persist($product1);
        $this->entityManager->persist($product2);
        
        $this->entityManager->persist($user);


        $this->entityManager->flush();

        $io->success("Byly nahrány úvodní data.");

        return Command::SUCCESS;
    }
}