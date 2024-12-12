<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{

  

    #[Route("/menu", name:"menu")]
    public function menu(): Response
    {
        $tags = $this->entityManager->getRepository(Tag::class)->findBy(['parentTag' => null]);

        return $this->render('menu.html.twig', [
            'tags' => $tags
        ]);
    }

    #[Route("/tag/{id}", name:"tag")]
    public function tag(int $id): Response
    {
        $tag = $this->entityManager->find(Tag::class, $id);

        $mainTags = $this->entityManager->getRepository(Tag::class)->findBy(['parentTag' => null]);

        return $this->render('tag.html.twig', [
            'tag' => $tag,
            'mainTags' => $mainTags
        ]);
    }
}
