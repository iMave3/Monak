<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShoppingCartController extends AbstractController
{
    #[Route("/shoppingcart", name:"shoppingcart")]
    public function contact(): Response
    {
        return $this->render('shoppingCart.html.twig', [
        ]);
    }

    #[Route("/cart/add/{id}", name: "add_cart")]
    public function menu(string $id): Response
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

        $this->saveCart($cart);

        return $this->redirectToRoute('tag', ['id' => $product->getTag()->getId()]);
    }

    #[Route("/cart/remove/{id}", name: "remove_cart")]
    public function removeTag(string $id): Response
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

            $this->saveCart($cart);
        }

        return $this->redirectToRoute('tag', ['id' => $product->getTag()->getId()]);
    }

}