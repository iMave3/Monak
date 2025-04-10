<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\ProductFormType;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    // CREATE -----------------------
    // Route pro vytvoření nového produktu
    #[Route("/product/create/{tagId}", name: "create_product")]
    public function createProduct(?string $tagId = null, Request $request): Response
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return $this->flashRedirect('error', 'Nemáte na tuto akci oprávnění.', 'main');
        }
        // Získání tagu podle ID, pokud není nalezen, vrátí chybu
        $tag = $this->entityManager->find(Tag::class, $tagId);
        if ($tag === null) {
            return $this->flashRedirect('error', 'Není nikde', 'main');
        }

        // Vytvoření formuláře pro nový produkt
        $form = $this->createForm(ProductFormType::class, null, ['imageRequired' => true]);

        // Zpracování formuláře
        $form->handleRequest($request);

        // Pokud je formulář odeslán a validní, pokračuje se v přidání produktu
        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();
            $image = $form->get('image')->getData();

            // Generování unikátního názvu souboru pro obrázek
            $newFileName = uniqid() . "." . $image->guessExtension();

            try {
                // Uložení obrázku do složky 'uploads'
                $image->move(
                    $this->getParameter('kernel.project_dir') . "/public/uploads",
                    $newFileName
                );
            } catch (FileException $e) {
                // Pokud dojde k chybě při nahrávání obrázku, vrátí chybovou odpověď
                return new Response($e->getMessage());
            }

            // Cesta k obrázku
            $imagePath = "/uploads/" . $newFileName;

            // Vytvoření nového produktu a přiřazení tagu
            $product = new Product($formData["name"], $imagePath, $formData["available"], $formData["price"]);
            $product->setTag($tag);

            // Uložení produktu do databáze
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            // Přesměrování zpět na stránku tagu
            return $this->redirectToRoute('tag', ["id" => $tagId]);
        }

        // Pokud formulář není odeslán nebo není validní, zobrazení formuláře
        return $this->render('createProduct.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // DISCONTINUE -----------------------
    // Route pro nastavení stavu produktu na 'discontinue' (vyřazený)
    #[Route("/product/set-discontinue/{id}", name: "set_discontinue_product")]
    public function setDiscontinueProduct(string $id, Request $request): Response
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return $this->flashRedirect('error', 'Nemáte na tuto akci oprávnění.', 'main');
        }
        // Získání produktu podle ID
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen!', 'main');
        }

        // Získání stavu z parametru query
        $state = $request->query->get('state');
        if ($state === null) {
            return $this->flashRedirect('error', 'Neposlán stav!', 'main');
        }

        // Nastavení stavu produktu na vyřazený a neaktivní
        $product->setDiscontinued($state);
        $product->setAvailable(false);

        // Uložení změn do databáze
        $this->entityManager->flush();

        // Přesměrování zpět na stránku tagu
        $tagId = $product->getTag()->getId();
        return $this->redirectToRoute('tag', ["id" => $tagId]);
    }

    // SET STOCK -----------------------
    // Route pro nastavení dostupnosti produktu (skladem/neve skladu)
    #[Route("/product/set-stock/{id}", name: "set_stock_product")]
    public function setStockProduct(string $id, Request $request): Response
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return $this->flashRedirect('error', 'Nemáte na tuto akci oprávnění.', 'main');
        }
        // Získání produktu podle ID
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen!', 'main');
        }

        // Získání stavu dostupnosti z query parametru
        $state = $request->query->get('state');
        if ($state === null) {
            return $this->flashRedirect('error', 'Neposlán stav!', 'main');
        }

        // Nastavení dostupnosti produktu
        $product->setAvailable($state);

        // Uložení změn do databáze
        $this->entityManager->flush();

        // Přesměrování zpět na stránku tagu
        $tagId = $product->getTag()->getId();
        return $this->redirectToRoute('tag', ["id" => $tagId]);
    }

    // REMOVE -----------------------
    // Route pro odstranění produktu
    #[Route("/product/remove/{id}", name: "remove_product")]
    public function removeProduct(string $id): Response
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return $this->flashRedirect('error', 'Nemáte na tuto akci oprávnění.', 'main');
        }

        // Získání produktu podle ID
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen!', 'main');
        }

        // Odstranění produktu z databáze
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Přesměrování na stránku tagu nebo vyprázdnění košíku
        $tagId = $product->getTag()->getId();
        return $this->redirectToRoute('clear_cart', []); // Nebo: return $this->redirectToRoute('tag', ["id" => $tagId]);
    }

    // EDIT -----------------------
    // Route pro úpravu produktu
    #[Route("/product/edit/{id}", name: "edit_product")]
    public function editProduct(string $id, Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            return $this->flashRedirect('error', 'Nemáte na tuto akci oprávnění.', 'main');
        }
        // Získání produktu podle ID
        $product = $this->entityManager->find(Product::class, $id);
        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen!', 'main');
        }

        // Vytvoření formuláře pro úpravu produktu
        $form = $this->createForm(ProductFormType::class, $product, ['imageRequired' => false]);

        // Zpracování formuláře
        $form->handleRequest($request);

        // Pokud je formulář odeslán a validní, pokračuje se v úpravě produktu
        if ($form->isSubmitted() and $form->isValid()) {
            $image = $form->get('image')->getData();

            // Pokud je obrázek změněn, validuje se a ukládá se nový obrázek
            if ($image !== null && count($validator->validate($image, new Image([
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, WEBP).',
                'maxSize' => '5M'
            ]))) == 0) {
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
                $product->setImageURL($imagePath);
            }

            // Uložení změn do databáze
            $this->entityManager->flush();

            // Přesměrování zpět na stránku tagu
            return $this->redirectToRoute('tag', ["id" => $product->getTag()->getId()]);
        }

        // Zobrazení formuláře pro úpravu produktu
        return $this->render("editProduct.html.twig", [
            "product" => $product,
            "form" => $form->createView()
        ]);
    }
}
