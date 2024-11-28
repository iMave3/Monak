<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

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
        $category = $this->entityManager->find(Category::class, 1);

        return $this->render('menu.html.twig', [
            'category' => $category
        ]);
    }

    #[Route("/contact", name:"contact")]
    public function contact(): Response
    {
        return $this->render('contact.html.twig', [
        ]);
    }
    #[Route("/myProfile", name:"myProfile")]
    public function myProfile(): Response
    {
        return $this->render('myProfile.html.twig', [
            'asdasd' => 'value'
        ]);
    }
}
