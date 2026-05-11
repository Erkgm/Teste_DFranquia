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
    public function index(CowRepository $cowRepo, FarmRepository $farmRepo, VeterinarianRepository $vetRepo): Response
    {
        //mostra o total de leite por semana
        $totalLeite = $cowRepo->createQueryBuilder('c')
            ->select('SUM(c.litrosLeitePorSemana)')
            ->where('c.abatido = false')
            ->getQuery()
            ->getSingleScalarResult()??0;

        //mostra a racao
        $totalRacao = $cowRepo->createQueryBuilder('c')
            ->select('SUM(c.racaoPorSemana)')
            ->where('c.abatido = false')
            ->getQuery()
            ->getSingleScalarResult()??0;

        //animal com 1 ano com o consumo maior que 500kg de raçaao
        $umAnoAtras = new \DateTime('-1 year');
        $jovensAltoConsumo = $cowRepo->createQueryBuilder('c')
            ->where('c.abatido = false')
            ->andWhere('c.dataNascimento >= :umAnoAtras')
            ->andWhere('c.racaoPorSemana > :consumo')
            ->setParameter('umAnoAtras', $umAnoAtras)
            ->setParameter('consumo', 500)
            ->getQuery()
            ->getResult();

        //animal para abate
        $todosVivos = $cowRepo->findBy(['abatido' =>false]);
        $elegiveis = array_filter($todosVivos, fn($c)=>$c->podeSerAbatido());


        return $this->render('dashboard/index.html.twig',[
            'totalLeite' => $totalLeite,
            'totalRacao' => $totalRacao,
            'jovensAltoConsumo' => $jovensAltoConsumo,
            'totalElegiveis' => count($elegiveis),
            'totalAnimais' => count($todosVivos),
            'totalFazendas' => count($farmRepo->findAll()),
            'totalVets' => count($vetRepo->findAll()),
        ]);
    }
}
