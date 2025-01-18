<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

class AdminController extends AbstractController
{
    #[Route("/adminOrders", name: "adminOrders")]
    public function index(): Response
    {

        return $this->render('adminOrders.html.twig', [

        ]);
    }
}
