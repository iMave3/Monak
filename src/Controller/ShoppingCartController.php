<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingCartController extends AbstractController
{
    #[Route("/shoppingcart", name:"shoppingcart")]
    public function contact(): Response
    {
        return $this->render('shoppingCart.html.twig', [
        ]);
    }
}