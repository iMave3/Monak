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

    protected function getGlobalParameters(): array
    {
        return [
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
                'quantity' => $productsArray['products'][$productId] ?? 0,
            ];
        }

        return [
            'products' => $displayableProducts,
            'total' => $cart['total'],
        ];
    }
}
