<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FarmController extends AbstractController
{
    #[Route('/farm', name: 'app_farm')]
    public function index(): Response
    {
        return $this->render('farm/index.html.twig', [
            'controller_name' => 'FarmController',
        ]);
    }
}
