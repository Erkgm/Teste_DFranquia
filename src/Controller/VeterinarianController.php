<?php

namespace App\Controller;

use App\Entity\Veterinarian;
use App\Form\VeterinarianType;
use App\Repository\VeterinarianRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/veterinarios', name: 'veterinarian_')]
class VeterinarianController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(VeterinarianRepository $repo, PaginatorInterface $paginator, Request $request) : Response
    {
        $query = $repo -> createQueryBuilder('v')
            ->orderBy('v.name', 'ASC')
            ->getQuery();

        $pagination = $paginator -> paginate($query, $request -> query -> getInt('page', 1), 10);

        return $this -> render('veterinarian/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/novo', name:'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, VeterinarianRepository $repo) : Response
    {
        $vet = new Veterinarian();
        $form = $this->createForm(VeterinarianType::class, $vet);
        $form -> handleRequest($request);

        //valida o CRMV do vet para não criar dois iguais
        if($form -> isSubmitted() && $form -> isValid()) {
            $existing = $repo->findOneBy(['crmv' => $vet->getCrmv()]);
            if ($existing){
                $this->addFlash('danger', "Já exite um veterinário com esse CRVM \" {$vet->getCrmv()}\".");
                return $this->render('veterinarian/form.html.twig',[
                    'form' => $form,
                    'title' => 'Novo Veterinário',
                ]);
            }
            $em -> persist($vet);
            $em -> flush();
            $this -> addFlash('success', 'Veterinário cadastrado');
            return $this -> redirectToRoute('veterinarian_index');
        }

        return $this -> render('veterinarian/form.html.twig', [
            'form' => $form,
            'title' => 'Novo Veterinário',
        ]);
    }

    #[Route('/{id}/editar', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Veterinarian $vet, Request $request, EntityManagerInterface $em, VeterinarianRepository $repo) : Response
    {
        $form = $this -> createForm(VeterinarianType::class, $vet);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            //validação do mesmo CRMV no editar
            $existing = $repo->createQueryBuilder('v')
                ->where('v.crmv = :crmv')
                ->andWhere('v.id != :id')
                ->setParameter('crmv', $vet->getCrmv())
                ->setParameter('id', $vet->getId())
                ->getQuery()
                ->getOneOrNullResult();

            if($existing){
                $this->addFlash('danger', "Já exite um veterinário com esse CRVM \" {$vet->getCrmv()}\".");
                return $this->render('veterinarian/form.html.twig',[
                    'form' => $form,
                    'title' => 'Editar Veterinário',
                    'vet' => $vet,
                ]);
            }
            $em -> flush();
            $this->addFlash('success', 'Veterinário atualizado');
            return $this ->redirectToRoute('veterinarian_index');
        }

        return $this -> render('veterinarian/form.html.twig', [
            'form' => $form,
            'title' => 'Editar Veterinário',
            'vet' => $vet,
        ]);
    }

    #[Route('/{id}/excluir', name: 'delete', methods: ['POST'])]
    public function delete(Veterinarian $vet, Request $request, EntityManagerInterface $em) : Response
    {
        if($this -> isCsrfTokenValid('delete-vet-' . $vet -> getId(), $request -> request -> get('_token'))) {
            $em -> remove($vet);
            $em -> flush();
            $this -> addFlash('success', 'Veterinário excluído');
        }

        return $this -> redirectToRoute('veterinarian_index');
    }
}
