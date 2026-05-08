<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CowController extends AbstractController
{
    #[Route('/cow', name: 'app_cow')]
    public function index(): Response
    {
        return $this->render('cow/index.html.twig', [
            'controller_name' => 'CowController',
        ]);
    }
}
