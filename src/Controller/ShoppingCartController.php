<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends AbstractController
{
    #[Route("/shoppingcart", name:"shoppingcart")]
    public function cart(): Response
    {
        return $this->render('shoppingCart.html.twig', [
        ]);
    }

    #[Route("/cart/add/{id}", name: "add_cart")]
    public function cartAdd(string $id): Response
    {
        $cart = $this->getCart();

        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        if (isset($cart['products'][$id])) {
            $cart['products'][$id]++;
        } else {
            $cart['products'][$id] = 1;
        }

        $cart['total'] += $product->getPrice(); 

        $this->saveCart($cart);

        return $this->redirectToRoute('shoppingcart');
    }

    #[Route("/cart/remove/{id}", name: "remove_cart")]
    public function cartRemove(string $id): Response
    {
        $cart = $this->getCart();

        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        if (isset($cart['products'][$id])) {

            $cart['products'][$id]--;

            if ($cart['products'][$id] == 0) {
                unset($cart['products'][$id]);
            }

            $cart['total'] -= $product->getPrice();

            $this->saveCart($cart);
        }

        return $this->redirectToRoute('shoppingcart');
    }

    #[Route("/cart/completeRemove/{id}", name: "complete_remove_cart")]
    public function completeRemove(string $id): Response
    {
        $cart = $this->getCart();

        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen', 'main');
        }

        if (isset($cart['products'][$id])) {

            $quantity = $cart['products'][$id];

                unset($cart['products'][$id]);

            $cart['total'] -= $product->getPrice() * $quantity;
            
            $this->saveCart($cart);
        }

        return $this->redirectToRoute('shoppingcart');
    }


}