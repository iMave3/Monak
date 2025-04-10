<?php

namespace App\Controller;

use App\Entity\Product;
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
    // Zobrazí hlavní stránku s hlavními tagy
    #[Route("/", name: "main")]
    public function menu(): Response
    {
        $isOnlyRender = false;
        // Načítá hlavní tagy (bez nadřazených tagů)
        $tags = $this->entityManager->getRepository(Tag::class)->findBy(['parentTag' => null]);

        return $this->render('tag.html.twig', [
            'tag' => null, // Není vybrán konkrétní tag
            'tags' => $tags, // Všechny hlavní tagy
            'mainTags' => $tags, // Pro zobrazení hlavních tagů v menu
            'currentTagId' => null, // Žádný tag není aktivní
            'isOnlyRender' => $isOnlyRender, // Určuje, zda jde jen o renderování
        ]);
    }

    // CREATE -----------------------
    // Vytvoření nového tagu
    #[Route("/tag/create/{parentId}", name: "create_tag", defaults: ["parentId" => null])]
    public function createTag(?string $parentId = null, Request $request): Response
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return $this->flashRedirect('error', 'Nemáte na tuto akci oprávnění.', 'main');
        }
        $parentTag = null;
        if ($parentId !== null) {
            // Pokud je zadán parentId, načteme rodičovský tag
            $parentTag = $this->entityManager->find(Tag::class, $parentId);
        }

        // Vytvoření formuláře pro tag, s volitelným rodičovským tagem
        $form = $this->createForm(TagFormType::class, null, ['imageRequired' => true]);
        $form->handleRequest($request);

        // Pokud je formulář odeslán a validní
        if ($form->isSubmitted() && $form->isValid()) {
            // Získání dat z formuláře
            $formData = $form->getData();
            $image = $form->get('image')->getData();
            $newFileName = uniqid() . "." . $image->guessExtension();

            try {
                // Pokusíme se uložit obrázek na server
                $image->move(
                    $this->getParameter('kernel.project_dir') . "/public/uploads",
                    $newFileName
                );
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }

            $imagePath = "/uploads/" . $newFileName;

            // Vytvoření nového tagu a přiřazení rodičovského tagu (pokud existuje)
            $tag = new Tag($formData['name'], $imagePath, $formData['description']);
            $tag->setParentTag($parentTag);

            // Uložení tagu do databáze
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            // Přesměrování na hlavní stránku nebo zpět na rodičovský tag
            if ($parentTag == null)
                return $this->redirectToRoute('main');
            else
                return $this->redirectToRoute('tag', ['id' => $parentTag->getId()]);
        }

        // Zobrazení formuláře pro vytvoření tagu
        return $this->render('create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // RENDER -----------------------
    // Zobrazení produktů pro konkrétní tag
    #[Route("/tag/{id}", name: "tag")]
    public function tag(int $id, Request $request): Response
    {
        $isOnlyRender = true;
        // Načítáme tag podle ID
        $tag = $this->entityManager->find(Tag::class, $id);
        $mainTags = $this->entityManager->getRepository(Tag::class)->findBy(['parentTag' => null]);

        // Načítáme parametry pro filtrování produktů
        $name = $request->query->get('name');
        $order = $request->query->get('order', 'name');
        $direction = strtoupper($request->query->get('direction', 'ASC'));
        $inStock = $request->query->getBoolean('in_stock');

        // Vytváříme dotaz na produkty spojené s tímto tagem
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->andWhere('p.tag = :tag')
            ->setParameter('tag', $tag);

        // Filtrování podle názvu
        if ($name) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        // Filtrování podle dostupnosti produktu
        if ($inStock) {
            $qb->andWhere('p.isAvailable = true');
        }

        // Seznam povolených polí pro seřazení
        $allowedOrderFields = ['name', 'price'];
        if (in_array($order, $allowedOrderFields)) {
            $qb->orderBy('p.' . $order, $direction);
        }

        // Získání filtrů produktů
        $products = $qb->getQuery()->getResult();

        // Zobrazení produktů pro tento tag
        return $this->render('tag.html.twig', [
            'tag' => $tag,
            'mainTags' => $mainTags,
            'currentTagId' => $id,
            'isOnlyRender' => $isOnlyRender,
            'products' => $products
        ]);
    }
    
    // REMOVE -----------------------
    // Odstranění tagu
    #[Route("/tag/remove/{id}", name: "remove_tag")]
    public function removeTag(string $id): Response
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return $this->flashRedirect('error', 'Nemáte na tuto akci oprávnění.', 'main');
        }
        // Načítání tagu podle ID
        $tag = $this->entityManager->find(Tag::class, $id);

        // Kontrola, zda tag existuje
        if ($tag === null) {
            return $this->flashRedirect('error', 'Tag nenalezen!', 'main');
        }

        $parentTag = $tag->getParentTag();

        // Odstranění tagu z databáze
        $this->entityManager->remove($tag);
        $this->entityManager->flush();

        // Přesměrování na rodičovský tag nebo na hlavní stránku
        if ($parentTag !== null) {
            return $this->redirectToRoute('tag', ['id' => $parentTag->getId()]);
        } else {
            return $this->redirectToRoute('main');
        }
    }

    // EDIT -----------------------
    // Úprava tagu
    #[Route("/tag/edit/{id}", name: "edit_tag")]
    public function editTag(string $id, Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return $this->flashRedirect('error', 'Nemáte na tuto akci oprávnění.', 'main');
        }
        // Načítání tagu podle ID
        $tag = $this->entityManager->find(Tag::class, $id);
        $form = $this->createForm(TagFormType::class, $tag, ['imageRequired' => false]);

        $form->handleRequest($request);

        // Pokud je formulář odeslán a validní
        if ($form->isSubmitted() and $form->isValid()) {
            $image = $form->get('image')->getData();

            // Pokud je obrázek změněn a validní
            if ($image !== null && count($validator->validate($image, new Image([
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, WEBP).',
                'maxSize' => '5M'
            ]))) == 0) {
                $newFileName = uniqid() . "." . $image->guessExtension();

                try {
                    // Pokusíme se uložit nový obrázek
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

            // Aktualizace dalších dat tagu
            $tag->setName($form->get("name")->getData());
            $tag->setDescription($form->get("description")->getData());

            // Uložení změn do databáze
            $this->entityManager->flush();

            // Přesměrování zpět na rodičovský tag nebo na hlavní stránku
            $parentTag = $tag->getParentTag();
            if ($parentTag !== null) {
                return $this->redirectToRoute('tag', ['id' => $parentTag->getId()]);
            } else {
                return $this->redirectToRoute('main');
            }
        }

        // Zobrazení formuláře pro editaci tagu
        return $this->render("edit.html.twig", [
            "tag" => $tag,
            "form" => $form->createView()
        ]);
    }
}
