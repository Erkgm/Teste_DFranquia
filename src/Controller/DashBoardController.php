<?php

namespace App\Controller;

use App\Repository\CowRepository;
use App\Repository\FarmRepository;
use App\Repository\VeterinarianRepository;
use App\Service\CowService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DashBoardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        CowRepository $cowRepo,
        FarmRepository $farmRepo,
        VeterinarianRepository $vetRepo,
        CowService $cowService,
    ): Response {
        $animaisVivos = $cowRepo->findAllAlive();
        $elegiveis = array_filter($animaisVivos, fn($cow) => $cowService->podeSerAbatido($cow));

        return $this->render('dashboard/index.html.twig', [
            'totalLeite'        => $cowRepo->getTotalLeitePorSemana(),
            'totalRacao'        => $cowRepo->getTotalRacaoPorSemana(),
            'jovensAltoConsumo' => $cowRepo->findJovemAltoConsumo(),
            'totalElegiveis'    => count($elegiveis),
            'totalAnimais'      => count($animaisVivos),
            'totalFazendas'     => count($farmRepo->findAll()),
            'totalVets'         => count($vetRepo->findAll()),
        ]);
    }
}