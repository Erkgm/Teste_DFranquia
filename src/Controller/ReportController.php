<?php

namespace App\Controller;

use App\Repository\CowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/relatorios', name: 'report_')]
class ReportController extends AbstractController
{
    #[Route('/slaughtered', name: 'slaughtered')]
    public function slaughtered(CowRepository $repo): Response
    {
        return $this->render('report/slaughtered.html.twig', [
            'animals' => $repo->findAllSlaughtered(),
        ]);
    }
}
