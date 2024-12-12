<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AbstractControllerBase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends AbstractControllerBase
{

    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        date_default_timezone_set("Europe/Prague");
    }

    protected function getGlobalParameters(): array
    {
        return [];
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
}
