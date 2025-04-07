<?php

namespace App\Controller;

use App\Entity\OrderSummary;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

class AdminController extends AbstractController
{
    #[Route("/adminOrders", name: "adminOrders")]
    public function index(): Response
    {

        $orderSummaries = $this->entityManager->getRepository(OrderSummary::class)->findAll();

        return $this->render('adminOrders.html.twig', [
            'orderSummaries' => $orderSummaries
        ]);
    }

    #[Route("/adminOrders/remove/{id}", name: "remove_order")]
    public function completeRemove(string $id): Response
    {
        $orderSummary = $this->entityManager->find(OrderSummary::class, $id);

        if ($orderSummary === null) {
            return $this->flashRedirect('error', 'Objednávka nenalezena', 'main');
        }
        
        else {
            
            $this->entityManager->remove($orderSummary);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('adminOrders');
    }
}
