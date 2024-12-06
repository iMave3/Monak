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

    // #[Route("/catalog/{name}", name:"catalog")]
    // public function catalog(string $name): Response
    // {
    //     $jsonFilePath = $this->getParameter('kernel.project_dir') . '/public/json/catalog.json';
    //     $jsonData = file_get_contents($jsonFilePath);
    //     $data = json_decode($jsonData, true);

    //     if (!key_exists($name, $data)) {
    //         return $this->flashRedirect('error', 'kategorie nenalezena', 'main');
    //     }

    //     $category = $data[$name];
        
    //     return $this->render('category.html.twig', [
    //         'category' => $category
    //     ]);
    // }
}
