<?php

namespace App\Controller;

use App\Entity\Farm;
use App\Form\FarmType;
use App\Repository\FarmRepository;
use App\Service\FarmService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fazendas', name: 'farm_')]
class FarmController extends AbstractController
{
    public function __construct(
        private FarmRepository $repo,
        private FarmService $service,
        private PaginatorInterface $paginator,
    ) {}


    //index
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query -> get('search', '');

        $qb = $search
            ? $this->repo->createQueryBuilder('f')
                ->where('LOWER(f.name) LIKE LOWER(:term)')
                ->orWhere('LOWER(f.responsavel) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $search . '%')
                ->orderBy('f.name', 'ASC')
            : $this->repo->createQueryBuilder('f')
            ->orderBy('f.name', 'ASC');

        $pagination = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 10);

        return $this->render('farm/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    //new
    #[Route('/novo', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $farm = new Farm();
        $form = $this->createForm(FarmType::class, $farm);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
            $error = $this->service->create($farm);
            if ($error){
                $this->addFlash('danger', $error);
            } else{
                $this -> addFlash('success', 'Fazenda cadastrado');
                return $this->redirectToRoute('farm_index');
            }
        }

        return $this -> render('farm/form.html.twig',[
            'form' => $form,
            'title' => 'Nova fazenda',
        ]);
    }

    //edita
    #[Route('/{id}/editar', name: 'edit', methods: ['GET', 'POST'] )]
    public function edit(Farm $farm, Request $request): Response
    {
        $form = $this -> createForm(FarmType::class, $farm);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $error = $this->service->update($farm);
            if ($error){
                $this->addFlash('danger', $error);
            } else {
                $this -> addFlash('success', 'Fazenda atualizada');
                return $this -> redirectToRoute('farm_index');
            }
        }

        return $this -> render('farm/form.html.twig', [
            'form' => $form,
            'title' => 'Editar fazenda',
            'farm' => $farm,
    ]);
    }

    //delete
    #[Route('/{id}/excluir', name: 'delete', methods: ['POST'])]
    public function delete(Farm $farm, Request $request): Response
    {
        if($this -> isCsrfTokenValid('delete-farm-' . $farm -> getId(), $request -> get('_token'))) {
            $error = $this->service->delete($farm);
            if($error){
                $this->addFlash('danger', $error);
            } else {
                $this -> addFlash('success', 'Fazenda excluída');
            }
        }

        return $this -> redirectToRoute('farm_index');

    }
}
