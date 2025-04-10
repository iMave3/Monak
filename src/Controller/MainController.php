<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ContactType;
use App\Service\EmailService;
use App\Utility\QueryParser;
use App\Utility\SearchTool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

class MainController extends AbstractController
{
    // Route pro zobrazení základní informace na stránce
    #[Route("/info", name:"info")]
    public function index(): Response
    {
        return $this->render('main.html.twig', [
            // Zde nejsou žádné parametry, pouze vykreslení šablony
        ]);
    }

    // Route pro zobrazení profilu uživatele
    #[Route("/myProfile", name:"myProfile")]
    public function myProfile(): Response
    {
        return $this->render('myProfile.html.twig', [
            // Zde nejsou žádné parametry, pouze vykreslení šablony pro profil
        ]);
    }
    
    // Route pro zobrazení kontaktního formuláře
    #[Route("/contact", name:"contact")]
    public function contact(Request $request, EmailService $emailService): Response
    {
        // Vytvoření formuláře na základě ContactType
        $form = $this->createForm(ContactType::class);

        // Zpracování formuláře při odeslání
        $form->handleRequest($request);

        // Kontrola, zda byl formulář odeslán a je validní
        if ($form->isSubmitted() && $form->isValid()) {
            // Získání dat z formuláře
            $formData = $form->getData();

            // Odeslání emailu pomocí EmailService
            $emailService->sendEmail(
                'plihal.marek@seznam.cz', // Adresa příjemce
                $formData['subject'], // Předmět emailu
                'mailContact.html.twig', // Šablona pro email
                [
                    'mail' => $formData['email'], // Email odesílatele
                    'message' => $formData['message'], // Zpráva
                    'subject' => $formData['subject'], // Předmět
                ]
            );

            // Po úspěšném odeslání přesměrování s flash zprávou
            return $this->flashRedirect('notice', 'Email byl úspěšně odeslán', 'contact');
        }

        // Pokud formulář není odeslán nebo není validní, vrátí se formulář na stránku
        return $this->render('contact.html.twig', [
            'form' => $form->createView() // Předání formuláře do šablony
        ]);
    }

    // Route pro vyhledávání produktů podle názvu
    #[Route("/search", name:"search")]
    public function search(Request $request): Response
    {
        // Získání hledaného názvu z query parametru
        $name = $request->query->get('name');

        // Vytvoření dotazu pro hledání produktů v databázi
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('p') // Výběr produktů
            ->from(Product::class, 'p') // Z tabulky produktů
            ->where('p.name LIKE :name') // Podmínka pro hledání názvu produktu
            ->setParameter('name', '%' . $name . '%') // Nastavení parametru pro hledání
            ->getQuery();
    
        // Získání výsledků dotazu
        $products = $query->getResult();

        // Vykreslení šablony pro zobrazení produktů
        return $this->render('search.html.twig', [
            'products' => $products // Předání nalezených produktů do šablony
        ]);
    }
}
