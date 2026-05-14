<?php

namespace App\Controller;

use App\Entity\Veterinarian;
use App\Form\VeterinarianType;
use App\Repository\VeterinarianRepository;
use App\Service\VeterinarianService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/veterinarios', name: 'veterinarian_')]
class VeterinarianController extends AbstractController
{
    public function __construct(
        private VeterinarianRepository $repo,
        private VeterinarianService $service,
        private PaginatorInterface $paginator,
    ) {}

    //index
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request) : Response
    {
        $search = $request->query-> get('search', '');
        $qb = $search
            ? $this->repo->createQueryBuilder('v')
                ->where('LOWER(v.name) LIKE LOWER(:term)')
                ->orWhere('LOWER(v.crmv) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $search . '%')
                ->orderBy('v.name', 'ASC')
            : $this->repo->createQueryBuilder('v')
                ->orderBy('v.name', 'ASC');

        $pagination = $this->paginator-> paginate($qb, $request -> query -> getInt('page', 1), 10);

        return $this -> render('veterinarian/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    //new
    #[Route('/novo', name:'new', methods: ['GET', 'POST'])]
    public function new(Request $request) : Response
    {
        $vet = new Veterinarian();
        $form = $this->createForm(VeterinarianType::class, $vet);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
           $error = $this->service->create($vet);
           if ($error){
               $this->addFlash('danget', $error);
           } else{
               $this->addFlash('success', 'Veterinário cadastrado');
               return $this->redirectToRoute('veterinarian_index');
           }
        }

        return $this -> render('veterinarian/form.html.twig', [
            'form' => $form,
            'title' => 'Novo Veterinário',
        ]);
    }

    //edit
    #[Route('/{id}/editar', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Veterinarian $vet, Request $request) : Response
    {
        $form = $this -> createForm(VeterinarianType::class, $vet);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $error = $this->service->update($vet);
            if ($error){
                $this->addFlash('danger', $error);
            } else{
                $this->addFlash('success', 'Veterinário atualizado');
                return $this->redirectToRoute('veterinarian_index');
            }

        }

        return $this -> render('veterinarian/form.html.twig', [
            'form' => $form,
            'title' => 'Editar Veterinário',
            'vet' => $vet,
        ]);
    }

    //delete
    #[Route('/{id}/excluir', name: 'delete', methods: ['POST'])]
    public function delete(Veterinarian $vet, Request $request) : Response
    {
        if($this -> isCsrfTokenValid('delete-vet-' . $vet -> getId(), $request -> request -> get('_token'))) {
            $error = $this->service->delete($vet);
            if ($error){
                $this->addFlash('danger', $error);
            } else{
                $this -> addFlash('success', 'Veterinário deletado');
            }
        }

        return $this -> redirectToRoute('veterinarian_index');
    }
}
