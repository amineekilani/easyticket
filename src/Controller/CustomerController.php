<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CustomerController extends AbstractController
{
    #[Route('/acceuil', name: 'app_customer')]
    public function index(): Response
    {
        return $this->render('customer/acceuil.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }
}
