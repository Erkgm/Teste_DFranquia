<?php

namespace App\Controller;

use App\Repository\CowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/relatorios', name: 'report_')]
class ReportController extends AbstractController
{
    #[Route('/abatidos', name: 'slaughtered')]
    public function slaughtered(CowRepository $repo): Response
    {
        $animais = $repo->createQueryBuilder('c')
            ->join('c.fazenda', 'f')
            ->addSelect('f')
            ->where('c.abatido = true')
            ->orderBy('c.abatidoEm', 'DESC')
            ->getQuery()
            ->getResult();
        return $this->render('report/slaughtered.html.twig', [
            'animais' => $animais,
        ]);
    }
}
