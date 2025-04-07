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

    #[Route("/info", name:"info")]
    public function index(): Response
    {
        return $this->render('main.html.twig', [
        ]);
    }

    #[Route("/myProfile", name:"myProfile")]
    public function myProfile(): Response
    {
        return $this->render('myProfile.html.twig', [

        ]);
    }
    #[Route("/contact", name:"contact")]
    public function contact(Request $request, EmailService $emailService): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $emailService->sendEmail(
                'plihal.marek@seznam.cz',
                $formData['subject'],
                'mailContact.html.twig',
                [
                    'mail' => $formData['email'],
                    'message' => $formData['message'],
                    'subject' => $formData['subject'],
                ]
            );

            return $this->flashRedirect('notice', 'Email byl úspěšně odeslán', 'contact');
        }

        return $this->render('contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route("/search", name:"search")]
    public function search(Request $request): Response
    {
        $name = $request->query->get('name');

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from(Product::class, 'p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();
    
        $products = $query->getResult();

        return $this->render('search.html.twig', [
            'products' => $products
        ]);
    }
}
