<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagFormType;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagController extends AbstractController
{

    // MENU -----------------------
    #[Route("/menu", name: "menu")]
    public function menu(): Response
    {
        $isOnlyRender = false;
        $tags = $this->entityManager->getRepository(Tag::class)->findBy(['parentTag' => null]);

        return $this->render('tag.html.twig', [
            'tag' => null,
            'tags' => $tags,
            'mainTags' => $tags,
            'currentTagId' => null,
            'isOnlyRender' => $isOnlyRender,
        ]);
    }

    
    // CREATE -----------------------
    #[Route("/tag/create/{parentId}", name: "create_tag", defaults: ["parentId" => null])]
    public function createTag(?string $parentId = null, Request $request): Response
    {
        $parentTag = null;
        if ($parentId !== null) {
            $parentTag = $this->entityManager->find(Tag::class, $parentId);
        }
        
        // Vytvoření formuláře s předaným seznamem tagů pro výběr parentTag
        $form = $this->createForm(TagFormType::class, null, ['imageRequired' => true]);
        
        $form->handleRequest($request);
        
        // Ověření, zda byl formulář odeslán a zda je platný
        if ($form->isSubmitted() && $form->isValid()) {
            // Vytvoření nového tagu
            $formData = $form->getData();
            
            $image = $form->get('image')->getData();
            
            $newFileName = uniqid() . "." . $image->guessExtension();
            
            try {
                $image->move(
                    $this->getParameter('kernel.project_dir') . "/public/uploads",
                    $newFileName
                );
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }
            
            $imagePath = "/uploads/" . $newFileName;
            
            $tag = new Tag($formData['name'], $imagePath, $formData['description']);
            $tag->setParentTag($parentTag);
            
            $this->entityManager->persist($tag);
            $this->entityManager->flush();
            
            
            if ($parentTag == null)
            return $this->redirectToRoute('menu');
        else
        return $this->redirectToRoute('tag', ['id' => $parentTag->getId()]);
}

return $this->render('create.html.twig', [
    'form' => $form->createView()
]);
}

// RENDER -----------------------
#[Route("/tag/{id}", name: "tag")]
public function tag(int $id): Response
{
    $isOnlyRender = true;
    $tag = $this->entityManager->find(Tag::class, $id);

    $mainTags = $this->entityManager->getRepository(Tag::class)->findBy(['parentTag' => null]);


    

    return $this->render('tag.html.twig', [
        'tag' => $tag,
        'mainTags' => $mainTags,
        'currentTagId' => $id,
        'isOnlyRender' => $isOnlyRender
    ]);
}
// REMOVE -----------------------
#[Route("/tag/remove/{id}", name: "remove_tag")]
public function removeTag(string $id): Response
{
    $tag = $this->entityManager->find(Tag::class, $id);
    
    if ($tag === null) {
        return $this->flashRedirect('error', 'Tag nenalezen!', 'main');
        }
    
        $parentTag = $tag->getParentTag();
        
        $this->entityManager->remove($tag);
        $this->entityManager->flush();
        
        if ($parentTag !== null) {
            return $this->redirectToRoute('tag', ['id' => $parentTag->getId()]);
        } else {
            return $this->redirectToRoute('menu');
        }
    }

    // EDIT -----------------------
    #[Route("/tag/edit/{id}", name: "edit_tag")]
    public function editTag(string $id, Request $request): Response
    {

        $tag = $this->entityManager->find(Tag::class, $id);
        $form = $this->createForm(TagFormType::class, $tag, ['imageRequired' => false]);

        // $form = $this->createForm(TagFormType::class, $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image !== null) {
                $newFileName = uniqid() . "." . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('kernel.project_dir') . "/public/uploads",
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $imagePath = "/uploads/" . $newFileName;
                $tag->setImageURL($imagePath);
            }

            $tag->setName($form->get("name")->getData());
            $tag->setDescription($form->get("description")->getData());
            
            $parentTag = $tag->getParentTag();

            $this->entityManager->flush();

        
            if ($parentTag !== null) {
                return $this->redirectToRoute('tag', ['id' => $parentTag->getId()]);
            } else {
                return $this->redirectToRoute('menu');
            }
        }

        return $this->render("edit.html.twig", [
            "tag" => $tag,
            "form" => $form->createView()
        ]);
    }
}