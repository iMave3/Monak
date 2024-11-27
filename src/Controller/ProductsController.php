<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{  
    #[Route("/category/{id}", name:"show_category")]
    public function showCategory(int $id): Response
    {

        $category = $this->entityManager->find(Category::class, $id);

        if ($category === null) {
            return $this->flashRedirect('error', 'Kategorie nebyla nalezena!', 'main');
        }

        return $this->render('category.html.twig', [
            'category' => $category
        ]);
    }
}
