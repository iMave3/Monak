<?php

namespace App\Controller;

use App\Entity\OrderSummary;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route("/adminOrders", name: "adminOrders")]
    public function index(Request $request): Response
    {
        $id = $request->query->get('id');

        $qb = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(OrderSummary::class, 'o');

        if ($id) {
            $qb->where('o.id = :id')
            ->setParameter('id',$id);
        }

        $qb->orderBy('o.created_at', 'DESC');

        $orderSummaries = $qb->getQuery()->getResult();

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

    #[Route("/adminOrders/set-state/{id}", name: "set_state_order")]
    public function setStateOrder(string $id, Request $request): Response
    {
        $orderSummary = $this->entityManager->find(OrderSummary::class, $id);

        if ($orderSummary === null) {
            return $this->flashRedirect('error', 'Objednávka nenalezena', 'main');
        }

        $status = $request->query->get('state');

        if ($status === null || !in_array($status, ['pending', 'delivered', 'taken', 'returned'])) {
            return $this->flashRedirect('error', 'Neplatný stav', 'main');
        }
        
        $orderSummary->setStatus($status);

        $this->entityManager->flush();

        return $this->redirectToRoute('adminOrders');
    }
}
