<?php

namespace App\Controller;

use App\Entity\CompanyInformation;
use App\Entity\OrderSet;
use App\Entity\OrderSummary;
use App\Entity\Product;
use App\Entity\UserInformation;
use App\Form\CompanyInformationType;
use App\Form\OrderInformationType;
use App\Form\UserInformationType;
use App\Service\EmailService;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends AbstractController
{
    // Route pro zobrazení košíku
    #[Route("/shoppingcart", name: "shoppingcart")]
    public function cart(): Response
    {
        return $this->render('shoppingCart.html.twig', []);
    }

    // Route pro přidání produktu do košíku
    #[Route("/cart/add/{id}", name: "add_cart")]
    public function cartAdd(string $id, Request $request): Response
    {
        // Načítání košíku z aktuální relace
        $cart = $this->getCart();

        // Načítání produktu podle ID
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        // Kontrola, zda je produkt vyřazen
        if ($product->isDiscontinued()) {
            return $this->flashRedirect('error', 'Produkt nelze přidat', 'main');
        }

        // Pokud je produkt již v košíku, zvýšíme jeho množství
        if (isset($cart['products'][$id])) {
            $cart['products'][$id]++;
        } else {
            $cart['products'][$id] = 1; // Jinak přidáme produkt do košíku s množstvím 1
        }

        // Aktualizace celkové ceny košíku
        $cart['total'] += $product->getPrice();

        // Uložení košíku
        $this->saveCart($cart);

        // Pokud je parametr 'toCart', přesměrujeme přímo na stránku košíku
        if ($request->query->has('toCart')) {
            return $this->redirectToRoute('shoppingcart');
        }

        // Přidání flash zprávy a přesměrování zpět na stránku tagu
        $this->addFlash("notice", "Produkt byl přidán do košíku");

        return $this->redirectToRoute('tag', [
            'id' => $product->getTag()->getId()
        ]);
    }

    // Route pro odstranění jednoho kusu produktu z košíku
    #[Route("/cart/remove/{id}", name: "remove_cart")]
    public function cartRemove(string $id): Response
    {
        // Načítání košíku
        $cart = $this->getCart();

        // Načítání produktu podle ID
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        // Pokud je produkt v košíku, snížíme jeho množství o 1
        if (isset($cart['products'][$id])) {
            $cart['products'][$id]--;

            // Pokud je množství 0, odstraníme produkt z košíku
            if ($cart['products'][$id] == 0) {
                unset($cart['products'][$id]);
            }

            // Aktualizace celkové ceny košíku
            $cart['total'] -= $product->getPrice();

            // Uložení změn do košíku
            $this->saveCart($cart);
        }

        // Přesměrování zpět na stránku košíku
        return $this->redirectToRoute('shoppingcart');
    }

    // Route pro kompletní odstranění produktu z košíku (včetně všech kusů)
    #[Route("/cart/completeRemove/{id}", name: "complete_remove_cart")]
    public function completeRemove(string $id): Response
    {
        // Načítání košíku
        $cart = $this->getCart();

        // Načítání produktu podle ID
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        // Pokud je produkt v košíku, odstraníme ho úplně
        if (isset($cart['products'][$id])) {
            $quantity = $cart['products'][$id];

            // Odstranění produktu z košíku
            unset($cart['products'][$id]);

            // Aktualizace celkové ceny košíku
            $cart['total'] -= $product->getPrice() * $quantity;

            // Uložení změn do košíku
            $this->saveCart($cart);
        }

        // Přesměrování zpět na stránku košíku
        return $this->redirectToRoute('shoppingcart');
    }

    // Route pro vyprázdnění košíku
    #[Route("/cart/clear", name: "clear_cart")]
    public function clearCart(): Response
    {
        // Vyprázdnění košíku
        $this->saveCart([
            'products' => [],
            'total' => 0
        ]);

        // Přesměrování na hlavní stránku
        return $this->redirectToRoute('main');
    }

    // Route pro zobrazení formuláře s informacemi pro objednávku
    #[Route("/shoppingcart-info", name: "shoppingcart_info")]
    public function cartInfo(Request $request): Response
    {
        // Vytvoření formuláře pro objednávkové informace
        $form = $this->createForm(OrderInformationType::class);
        $form->handleRequest($request);

        // Pokud je formulář odeslán a validní, uložíme data a přesměrujeme
        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            $session = $this->requestStack->getSession();
            $session->set('userInformation', $formData['userInformation']);

            // Uložení firemních informací, pokud existují
            $companyInformation = $formData['companyInformation'];
            if ($companyInformation !== null) {
                $session->set('companyInformation', $formData['companyInformation']);
            }

            // Přesměrování na stránku s rekapitulací objednávky
            return $this->redirectToRoute('shoppingcart_summary');
        }

        // Zobrazení formuláře pro zadání informací
        return $this->render('shoppingCartInfo.html.twig', [
            'form' => $form
        ]);
    }

    // Route pro zobrazení shrnutí objednávky a potvrzení
    #[Route("/shoppingcart-summary", name: "shoppingcart_summary")]
    public function cartSummary(Request $request, EmailService $emailService): Response
    {
        // Načítání informací o uživatelských údajích z relace
        $session = $this->requestStack->getSession();
        $userInformation = $session->get('userInformation');

        // Kontrola, zda byly zadány informace pro platbu
        if ($userInformation === null) {
            return $this->flashRedirect('error', 'Nebyly zadány informace k platbě', "shoppingcart_info");
        }

        // Načítání firemních údajů, pokud existují
        $companyInformation = $session->get('companyInformation');

        // Vytvoření formuláře pro potvrzení objednávky
        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class, ['label' => 'Potvrdit objednávku'])
            ->getForm();

        $form->handleRequest($request);

        // Pokud je formulář odeslán a validní, dokončíme objednávku
        if ($form->isSubmitted() && $form->isValid()) {
            $totalPrice = 0;

            // Vytvoření objektu pro souhrn objednávky
            $orderSummary = new OrderSummary(
                $totalPrice,
                $userInformation
            );

            // Pro každý produkt v košíku vytvoříme objednávkový set
            foreach ($this->getCart()['products'] as $id => $quantity) {
                $product = $this->entityManager->find(Product::class, $id);
                if ($product === null) {
                    return $this->flashRedirect('error', 'Produkt nebyl nalezen', 'clear_cart');
                }

                $orderSet = new OrderSet($quantity, $product->getPrice());

                $orderSummary->addOrderSet($orderSet);

                // Aktualizace celkové ceny objednávky
                $totalPrice += $product->getPrice() * $quantity;

                $product->addOrderSet($orderSet);

                $this->entityManager->persist($orderSet);
            }

            // Nastavení celkové ceny objednávky
            $orderSummary->setTotalPrice($totalPrice);

            // Pokud byly zadány firemní údaje, přiřadíme je k objednávce
            if ($companyInformation && $companyInformation->getCompanyName() !== null && $companyInformation->getIco() !== null && $companyInformation->getDic() !== null) {
                $companyInformation->setOrderSummary($orderSummary);
                $this->entityManager->persist($companyInformation);
            }

            // Uložení uživatelských a objednávkových informací do databáze
            $userInformation->setOrderSummary($orderSummary);

            $this->entityManager->persist($userInformation);
            $this->entityManager->persist($orderSummary);

            $this->entityManager->flush();

            // Odeslání emailu s potvrzením objednávky
            $emailService->sendEmail($userInformation->getMail(), 'Objednávka byla dokončena', 'mailOrderComplete.html.twig', [
                'userInformation' => $userInformation
            ]);

            // Přesměrování na stránku s potvrzením objednávky
            return $this->flashRedirect('notice', 'Objednávka byla dokončena!', 'clear_cart');
        }

        // Zobrazení shrnutí objednávky a formuláře pro potvrzení
        return $this->render('shoppingCartSummary.html.twig', [
            'form' => $form,
            'userInformation' => $userInformation,
            'companyInformation' => $companyInformation,  // Přidání firemních informací
        ]);
    }
}
