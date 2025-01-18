<?php

namespace App\Controller;

use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;

class SearchItemsController extends AbstractController
{
    /**
     * @Route("/search", name="app_search")
     */
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(SearchType::class);

        $form->handleRequest($request);

        $products = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();
            $products = $productRepository->searchByQuery($query);
        }

        return $this->render('base.html.twig', [
            'form' => $form->createView(),
            'products' => $products,
        ]);
    }
}
