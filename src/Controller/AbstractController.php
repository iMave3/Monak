<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AbstractControllerBase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends AbstractControllerBase
{

    protected EntityManagerInterface $entityManager;
    protected RequestStack $requestStack;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        date_default_timezone_set("Europe/Prague");
    }
    // uz by to melo fungovat jak ten total tak spravny prevadeni, pridal jsem ti tam debug_cart coz je ten officialni kosik a ten displaycart je jen ta nadstavba ktera muze byt spatne 
    // (byla ale nemela by uz byt)
    protected function getGlobalParameters(): array
    {
        return [
            'debug_cart' => $this->getCart(),
            'cart' => $this->getDisplayCart()
        ];
    }

    protected function renderView(string $view, array $parameters = []): string
    {
        return get_parent_class()::renderView($view, array_merge($parameters, $this->getGlobalParameters()));
    }

    protected function render(string $view, array $parameters = [], Response $response = null) : Response
    {
        return get_parent_class()::render($view, array_merge($parameters, $this->getGlobalParameters()));
    }

    protected function flashRedirect(
        string $type,
        string $message,
        string $route,
        ?array $parameters = []
    ): RedirectResponse {
        $this->addFlash($type, $message);
        return $this->redirectToRoute($route, $parameters);
    }

    protected function getCart() : array
    {
        $session = $this->requestStack->getSession();

        return $session->get('cart', [
            'products' => [],
            'total' => 0,
        ]);
    }

    // protected function getUdaje() : array
    // {
    //     $session = $this->requestStack->getSession();
    // }

    protected function saveCart(array $cart) : void
    {
        $session = $this->requestStack->getSession();

        $session->set('cart', $cart);
    }

    protected function getDisplayCart() : array
    {
        $cart = $this->getCart();

        $productIds = array_keys($cart['products']);

        $products = $this->entityManager->getRepository(Product::class)->findBy(['id' => $productIds]);

        $displayableProducts = [];
        foreach ($products as $product) {
            $productId = $product->getId();
            $displayableProducts[] = [
                'product' => $product, // The Doctrine entity
                'quantity' => $cart['products'][$productId],
            ];
        }

        return [
            'products' => $displayableProducts,
            'total' => $cart['total'],
        ];
    }
}
