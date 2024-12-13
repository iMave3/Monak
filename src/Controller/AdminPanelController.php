<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;

class AdminPanelController extends AbstractController
{
    #[Route("/admin", name:"adminPanel")]
    public function index(): Response
    {
        return $this->render('adminPanel.html.twig', [
        ]);
    }
}
