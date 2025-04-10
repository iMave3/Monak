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
    // EntityManager a RequestStack jsou injektovány prostřednictvím konstruktoru
    protected EntityManagerInterface $entityManager;
    protected RequestStack $requestStack;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        date_default_timezone_set("Europe/Prague"); // Nastavení výchozího časového pásma
    }

    // Pomocná funkce pro získání globálních parametrů pro vykreslení šablony
    protected function getGlobalParameters(): array
    {
        return [
            'debug_cart' => $this->getCart(), // Aktuální data z košíku
            'cart' => $this->getDisplayCart(), // Zpracovaný košík pro zobrazení
            'search_url' => $this->generateUrl('search') // URL pro vyhledávací routu
        ];
    }

    // Vykreslí šablonu a přidá globální parametry k těm předaným
    protected function renderView(string $view, array $parameters = []): string
    {
        return get_parent_class()::renderView($view, array_merge($parameters, $this->getGlobalParameters()));
    }

    // Vykreslí šablonu a vrátí Response objekt s globálními parametry
    protected function render(string $view, array $parameters = [], Response $response = null) : Response
    {
        return get_parent_class()::render($view, array_merge($parameters, $this->getGlobalParameters()));
    }

    // Přidá flash zprávu a přesměruje na danou routu s volitelnými parametry
    protected function flashRedirect(
        string $type,
        string $message,
        string $route,
        ?array $parameters = []
    ): RedirectResponse {
        $this->addFlash($type, $message); // Přidání flash zprávy
        return $this->redirectToRoute($route, $parameters); // Přesměrování na specifikovanou routu
    }

    // Získá data o košíku ze session
    protected function getCart() : array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', [
            'products' => [], // Produkty v košíku
            'total' => 0, // Celková cena
        ]);
    }
    
    // Uloží aktuální stav košíku do session
    protected function saveCart(array $cart) : void
    {
        $session = $this->requestStack->getSession();
        $session->set('cart', $cart); // Nastaví nový stav košíku
    }

    // Získá produkty pro zobrazení v košíku
    protected function getDisplayCart() : array
    {
        $cart = $this->getCart(); // Získání aktuálního košíku
        $productIds = array_keys($cart['products']); // Získání ID produktů v košíku
        $products = $this->entityManager->getRepository(Product::class)->findBy(['id' => $productIds]); // Načtení produktů z databáze

        $displayableProducts = []; // Pole pro produkty, které budou zobrazeny
        foreach ($products as $product) {
            $productId = $product->getId();
            $displayableProducts[] = [
                'product' => $product, // Doctrine entita produktu
                'quantity' => $cart['products'][$productId], // Množství produktu v košíku
            ];
        }

        return [
            'products' => $displayableProducts, // Zobrazené produkty
            'total' => $cart['total'], // Celková cena košíku
        ];
    }
}
