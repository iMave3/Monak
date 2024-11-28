<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

class MenuController extends AbstractController
{  
    #[Route("/menu", name:"menu")]
    public function menu(): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->findAll();  

        return $this->render('menu.html.twig', [
            'category' => $category
        ]);
    }
}
