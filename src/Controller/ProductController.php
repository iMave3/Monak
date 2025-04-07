<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Tag;
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
    #[Route("/product/create/{tagId}", name: "create_product")]
    public function createProduct(?string $tagId = null, Request $request): Response
    {
        $tag = $this->entityManager->find(Tag::class, $tagId);
        if ($tag === null) {
            return $this->flashRedirect('error', 'Neni nikde', 'main');
        }

        $form = $this->createForm(ProductFormType::class, null, ['imageRequired' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

            $product = new Product($formData["name"], $imagePath, $formData["available"], $formData["price"]);
            $product->setTag($tag);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('tag', ["id" => $tagId]);
        }

        return $this->render('createProduct.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // DISCONTINUE -----------------------
    #[Route("/product/set-discontinue/{id}", name: "set_discontinue_product")]
    public function setDiscontinueProduct(string $id, Request $request): Response
    {
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen!', 'main');
        }
        $state = $request->query->get('state');
        if ($state === null) {
            return $this->flashRedirect('error', 'Neposlán stav!', 'main');
        }

        $product->setDiscontinued($state);

        $this->entityManager->flush();

        $tagId = $product->getTag()->getId();

        return $this->redirectToRoute('tag', ["id" => $tagId]);
    }

    // SET STOCK -----------------------
    #[Route("/product/set-stock/{id}", name: "set_stock_product")]
    public function setStockProduct(string $id, Request $request): Response
    {
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen!', 'main');
        }
        $state = $request->query->get('state');
        if ($state === null) {
            return $this->flashRedirect('error', 'Neposlán stav!', 'main');
        }

        $product->setAvailable($state);

        $this->entityManager->flush();

        $tagId = $product->getTag()->getId();

        return $this->redirectToRoute('tag', ["id" => $tagId]);
    }

    // EDIT -----------------------
    #[Route("/product/edit/{id}", name: "edit_product")]
    public function editProduct(string $id, Request $request): Response
    {
        $product = $this->entityManager->find(Product::class, $id);
        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen!', 'main');
        }
        $form = $this->createForm(ProductFormType::class, $product, ['imageRequired' => false]);

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
                $product->setImageURL($imagePath);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('tag', ["id" => $product->getTag()->getId()]);
        }

        return $this->render("editProduct.html.twig", [
            "product" => $product,
            "form" => $form->createView()
        ]);
    }
}
