<?php

namespace App\Controller;

use App\Entity\Farm;
use App\Form\FarmType;
use App\Repository\FarmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fazendas', name: 'farm_')]
class FarmController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(FarmRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $repo -> createQueryBuilder('f')
            ->orderBy('f.name', 'ASC')
            ->getQuery();

        $pagination = $paginator -> paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('farm/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/novo', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $farm = new Farm();
        $form = $this->createForm(FarmType::class, $farm);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
            $em -> persist($farm);
            $em -> flush();
            $this -> addFlash('success', 'Fazenda cadastrada');
            return $this -> redirectToRoute('farm_index');
        }

        return $this -> render('farm/form.html.twig',[
            'form' => $form,
            'title' => 'Nova fazenda',
        ]);
    }

    #[Route('/{id}/editar', name: 'edit', methods: ['GET', 'POST'] )]
    public function edit(Farm $farm, Request $request, EntityManagerInterface  $em): Response
    {
        $form = $this -> createForm(FarmType::class, $farm);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $em -> flush();
            $this -> addFlash('success', 'Fazenda atualizada');
            return $this -> redirectToRoute('farm_index');
        }

        return $this -> render('farm/form.html.twig', [
            'form' => $form,
            'title' => 'Editar fazenda',
            'farm' => $farm,
    ]);
    }

    #[Route('/{id}/excluir', name: 'delete', methods: ['POST'])]
    public function delete(Farm $farm, Request $request, EntityManagerInterface $em): Response
    {
        if($this -> isCsrfTokenValid('delete-farm-' . $farm -> getId(), $request -> get('_token'))) {
            $em -> remove($farm);
            $em -> flush();
            $this -> addFlash('success', 'Fazenda excluída');
        }

        return $this -> redirectToRoute('farm_index');

    }
}
