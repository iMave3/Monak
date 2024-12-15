<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagFormType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
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

    #[Route("/tag/create", name:"create_tag")]
    public function createTag(Request $request): Response
    { 
        // Vytvoření nového tagu
        $tag = new Tag("", "");
        
        // Načtení všech tagů, které budou k dispozici pro výběr nadřazeného tagu
        $tags = $this->entityManager->getRepository(Tag::class)->findBy(['parentTag' => null]);
    
        // Vytvoření formuláře s předaným seznamem tagů pro výběr parentTag
        $form = $this->createForm(TagFormType::class, $tag, [
            'tags' => $tags,  // Předání seznamu hlavních tagů do formuláře
        ]);
    
        $form->handleRequest($request);
    
        // Ověření, zda byl formulář odeslán a zda je platný
        if ($form->isSubmitted() && $form->isValid()) {
            $newTag = $form->getData();

// Ve vašem kontroleru
$imagePath = $form->get("imagePath")->getData();
if ($imagePath) {
    $newFileName = uniqid() . "." . $imagePath->guessExtension();

    try {
        $imagePath->move(
            $this->getParameter('kernel.project_dir') . "/public/uploads",  // Uložení do public/uploads
            $newFileName
        );
    } catch (FileException $e) {
        return new Response($e->getMessage());
    }

    // Nastavení cesty k obrázku pro entitu
    $newTag->setImagePath("/uploads/" . $newFileName);
}

            // Persistování nového tagu
            $this->entityManager->persist($newTag);
            $this->entityManager->flush();
    
            // Přesměrování na stránku menu po úspěšném přidání tagu
            return $this->redirectToRoute('menu');
        }
    
        return $this->render('create.html.twig', [
            'form' => $form->createView()
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
