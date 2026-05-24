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
        $animalsAlive = $cowRepo->findAllAlive();
        $eligible  = array_filter($animalsAlive, fn($cow) => $cowService->canBeSlaughtered($cow));

        return $this->render('dashboard/index.html.twig', [
            'totalMilk'        => $cowRepo->getTotalMilkPerWeek(),
            'totalRation'        => $cowRepo->getTotalRationPerWeek(),
            'youngHeavyEaters' => $cowRepo->findYoungHeavyEaters(),
            'totalEligible'    => count($eligible ),
            'totalAnimals'      => count($animalsAlive),
            'totalFarms'     => count($farmRepo->findAll()),
            'totalVets'         => count($vetRepo->findAll()),
        ]);
    }
}