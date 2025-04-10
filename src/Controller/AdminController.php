<?php

namespace App\Controller;

use App\Entity\OrderSummary;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    // Route pro zobrazení přehledu objednávek
    #[Route("/adminOrders", name: "adminOrders")]
    public function index(Request $request): Response
    {
        // Získání ID z query parametru, pokud je přítomné
        $id = $request->query->get('id');

        // Vytvoření dotazu pro získání objednávek
        $qb = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(OrderSummary::class, 'o');

        // Pokud je ID, přidáme podmínku pro filtrování podle ID objednávky
        if ($id) {
            $qb->where('o.id = :id')
                ->setParameter('id', $id);
        }

        // Seřazení objednávek podle data vytvoření v sestupném pořadí
        $qb->orderBy('o.created_at', 'DESC');

        // Získání výsledků dotazu
        $orderSummaries = $qb->getQuery()->getResult();

        // Pro každou objednávku přidáme přeložený stav
        foreach ($orderSummaries as $order) {
            $order->translatedStatus = $this->translateStatus($order->getStatus());
        }

        // Vykreslení šablony s objednávkami
        return $this->render('adminOrders.html.twig', [
            'orderSummaries' => $orderSummaries
        ]);
    }

    // Route pro odstranění objednávky
    #[Route("/adminOrders/remove/{id}", name: "remove_order")]
    public function completeRemove(string $id): Response
    {
        // Načtení objednávky podle ID
        $orderSummary = $this->entityManager->find(OrderSummary::class, $id);

        // Pokud objednávka neexistuje, vrátíme chybu
        if ($orderSummary === null) {
            return $this->flashRedirect('error', 'Objednávka nenalezena', 'main');
        } else {
            // Odstraníme objednávku a provedeme flush
            $this->entityManager->remove($orderSummary);
            $this->entityManager->flush();
        }

        // Po odstranění objednávky přesměrujeme zpět na seznam objednávek
        return $this->redirectToRoute('adminOrders');
    }

    // Route pro změnu stavu objednávky
    #[Route("/adminOrders/set-state/{id}", name: "set_state_order")]
    public function setStateOrder(string $id, Request $request): Response
    {
        // Načtení objednávky podle ID
        $orderSummary = $this->entityManager->find(OrderSummary::class, $id);

        // Pokud objednávka neexistuje, vrátíme chybu
        if ($orderSummary === null) {
            return $this->flashRedirect('error', 'Objednávka nenalezena', 'main');
        }

        // Získání nového stavu objednávky z query parametrů
        $status = $request->query->get('state');

        // Ověření, že stav je platný
        if ($status === null || !in_array($status, ['pending', 'delivered', 'taken', 'returned'])) {
            return $this->flashRedirect('error', 'Neplatný stav', 'main');
        }

        // Nastavení nového stavu objednávky
        $orderSummary->setStatus($status);

        // Uložení změn do databáze
        $this->entityManager->flush();

        // Po změně stavu přesměrujeme zpět na seznam objednávek
        return $this->redirectToRoute('adminOrders');
    }

    // Pomocná funkce pro přeložení stavu objednávky
    public function translateStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'Probíhající',    // Stav: Probíhající
            'delivered' => 'Zasláno',      // Stav: Zasláno
            'taken' => 'Převzato',         // Stav: Převzato
            'returned' => 'Vráceno',       // Stav: Vráceno
            default => 'Neznámý stav',     // Neznámý stav
        };
    }
}
