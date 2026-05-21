<?php

namespace App\Controller;

use App\Entity\Cow;
use App\Form\CowType;
use App\Repository\CowRepository;
use App\Repository\FarmRepository;
use App\Service\CowService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/animais', name: 'cow_')]
class CowController extends AbstractController
{
    public function __construct(
        private CowRepository $repo,
        private CowService $service,
        private PaginatorInterface $paginator,
    ) {}

    //index
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, FarmRepository $farmRepo): Response
    {
        $farmId = $request->query->getInt('farm_id') ?: null;

        $pagination = $this->paginator->paginate($this->repo->createListQueryBuilder($farmId), $request->query->getInt('page', 1), 15);

        return $this->render('cow/index.html.twig', [
            'pagination' => $pagination,
            'farms'      => $farmRepo->findAll(),
            'selectedFarmId' => $farmId,
        ]);
    }

    //new
    #[Route('/novo', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $cow = new Cow();
        $form = $this->createForm(CowType::class, $cow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->service->create($cow);
            if ($error) {
                $this->addFlash('danger', $error);
            } else {
                $this->addFlash('success', 'Animal cadastrado');
                return $this->redirectToRoute('cow_index');
            }
        }

        return $this->render('cow/form.html.twig', [
            'form'  => $form,
            'title' => 'Novo Animal',
        ]);
    }

    //edit
    #[Route('/{id}/editar', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Cow $cow, Request $request): Response
    {
        if ($cow->isAbatido()) {
            $this->addFlash('warning', 'Animais abatidos não podem ser editados');
            return $this->redirectToRoute('cow_index');
        }

        $oldFarmId = $cow->getFazenda()?->getId();

        $form = $this->createForm(CowType::class, $cow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->service->update($cow, $oldFarmId);
            if ($error) {
                $this->addFlash('danger', $error);
            } else {
                $this->addFlash('success', 'Animal atualizado');
                return $this->redirectToRoute('cow_index');
            }
        }

        return $this->render('cow/form.html.twig', [
            'form'  => $form,
            'title' => 'Editar Animal',
            'cow'   => $cow,
        ]);
    }

    //delete animal
    #[Route('/{id}/excluir', name: 'delete', methods: ['POST'])]
    public function delete(Cow $cow, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete-cow-' . $cow->getId(), $request->get('_token'))) {
            $codigo = $cow->getCodigo();
            $this->repo->getEntityManager()->remove($cow);
            $this->repo->getEntityManager()->flush();
            $this->addFlash('success', "Animal \"{$codigo}\" removido");
        }

        return $this->redirectToRoute('cow_index');
    }

    //lista dos animais de abate
    #[Route('/lista-abate', name: 'slaughter_list', methods: ['GET'])]
    public function slaughterList(): Response
    {
        $animals = $this->repo->findAllAlive();

        $eligible = [];
        foreach ($animals as $cow) {
            if ($this->service->podeSerAbatido($cow)) {
                $eligible[] = [
                    'cow'     => $cow,
                    'motivos' => $this->service->getMotivoAbate($cow),
                ];
            }
        }
        return $this->render('cow/slaughter_list.html.twig', [
            'eligible' => $eligible,
        ]);
    }

    //pronto para abate
    #[Route('/{id}/abater', name: 'slaughter', methods: ['POST'])]
    public function slaughter(Cow $cow, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('abate-' . $cow->getId(), $request->get('_token'))) {
            $this->addFlash('danger', 'Token inválido.');
            return $this->redirectToRoute('cow_slaughter_list');
        }

        $error = $this->service->slaughter($cow);
        if ($error) {
            $this->addFlash('danger', $error);
        } else {
            $this->addFlash('success', "Animal \"{$cow->getCodigo()}\" enviado para abate");
        }

        return $this->redirectToRoute('cow_slaughter_list');
    }
}