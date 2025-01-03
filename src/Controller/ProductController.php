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
            return $this->flashRedirect('error', 'Neni nikde', 'menu');
        }

        // Vytvoření formuláře s předaným seznamem tagů pro výběr parentTag
        $form = $this->createForm(ProductFormType::class, null, ['data' => ['imageRequired' => true]]);

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

            $product = new Product($formData["name"], $imagePath, $formData["isAvailable"], $formData["price"]);
            $product->setTag($tag);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('tag', ["id"=>$tagId]);
        }

        return $this->render('createProduct.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // REMOVE -----------------------
    #[Route("/product/remove/{id}", name: "remove_product")]
    public function removeProduct(string $id): Response
    {
        $product = $this->entityManager->find(Product::class, $id);

        if ($product === null) {
            return $this->flashRedirect('error', 'Produkt nenalezen!', 'main');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->redirectToRoute('menu');
    }

    // EDIT -----------------------
    #[Route("/product/edit/{id}", name: "edit_product")]
    public function editProduct(string $id, Request $request): Response
    {
        $product = $this->entityManager->find(Product::class, $id);
        $form = $this->createForm(ProductFormType::class, $product, ['data' => ['imageRequired' => false]]);

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

            $product->setName($form->get("name")->getData());
            $product->setAvailable($form->get("isAvailable")->getData());
            $product->setPrice($form->get("price")->getData());

            $this->entityManager->flush();

            return $this->redirectToRoute('tag', ["id"=>$product->getTag()->getId()]);
        }

        return $this->render("editProduct.html.twig", [
            "product" => $product,
            "form" => $form->createView()
        ]);
    }
}
