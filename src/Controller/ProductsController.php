<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{  
    #[Route("/tag/{id}", name:"show_tag")]
    public function showTag(int $id): Response
    {

        $tag = $this->entityManager->find(Tag::class, $id);

        if ($tag === null) {
            return $this->flashRedirect('error', 'Kategorie nebyla nalezena!', 'main');
        }

        return $this->render('tag.html.twig', [
            'tag' => $tag
        ]);
    }
}
