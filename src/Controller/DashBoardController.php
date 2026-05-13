<?php

namespace App\Controller;

use App\Repository\CowRepository;
use App\Repository\FarmRepository;
use App\Repository\VeterinarianRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DashBoardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        CowRepository $cowRepo,
        FarmRepository $farmRepo,
        VeterinarianRepository $vetRepo
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'totalLeite'        => $cowRepo->getTotalLeitePorSemana(),
            'totalRacao'        => $cowRepo->getTotalRacaoPorSemana(),
            'jovensAltoConsumo' => $cowRepo->findJovemAltoConsumo(),
            'totalElegiveis'    => count($cowRepo->findProntosParaAbate()),
            'totalAnimais'      => count($cowRepo->findTodosVivos()),
            'totalFazendas'     => count($farmRepo->findAll()),
            'totalVets'         => count($vetRepo->findAll()),
        ]);
    }
}