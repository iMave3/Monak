<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

class MainController extends AbstractController
{

    #[Route("/", name:"main")]
    public function index(): Response
    {
        return $this->render('main.html.twig', [
        ]);
    }

    #[Route("/myProfile", name:"myProfile")]
    public function myProfile(): Response
    {
        return $this->render('myProfile.html.twig', [

        ]);
    }
    #[Route("/contact", name:"contact")]
    public function contact(): Response
    {
        return $this->render('contact.html.twig', [
        ]);
    }
}
