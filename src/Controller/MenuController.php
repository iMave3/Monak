<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

class MenuController extends AbstractController
{  
    #[Route("/tag/{name}", name:"tag")]
    public function tag(string $name): Response
    {
        $tag = $this->entityManager->getRepository(Tag::class)->findBy(['name' => $name]);  

        return $this->render('tag.html.twig', [
            'tag' => $tag
        ]);
    }

    #[Route("/catalog/{name}", name:"catalog")]
    public function catalog(string $name): Response
    {
        $jsonFilePath = $this->getParameter('kernel.project_dir') . '/public/json/catalog.json';
        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);

        if (!key_exists($name, $data)) {
            return $this->flashRedirect('error', 'kategorie nenalezena', 'main');
        }

        $category = $data[$name];
        
        return $this->render('category.html.twig', [
            'category' => $category
        ]);
    }
}
