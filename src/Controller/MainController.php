<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{  
    #[Route("/", name:"main")]
    public function index(): Response
    {
        return $this->render('main.html.twig', [
        ]);
    }
    #[Route("/menu", name:"menu")]
    public function menu(): Response
    {
        return $this->render('menu.html.twig', [
        ]);
    }
    #[Route("/contact", name:"contact")]
    public function contact(): Response
    {
        return $this->render('contact.html.twig', [
        ]);
    }
}
