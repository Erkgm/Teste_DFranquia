<?php

namespace App\Controller;

use App\Entity\Cow;
use App\Form\CowType;
use App\Repository\CowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/animais', name: 'cow_')]
class CowController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CowRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $repo -> createQueryBuilder('c')
            ->where('c.abatido = false')
            ->orderBy('c.codigo', 'ASC')
            ->getQuery();

        $pagination = $paginator -> paginate($query, $request -> query -> getInt('page', 1), 10);

        return $this->render('cow/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/novo', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $cow = new Cow();
        $form = $this ->createForm(CowType::class, $cow);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
            $cow -> setAbatido(false);
            $em -> persist($cow);
            $em ->flush();
            $this -> addFlash('success', 'Animal cadastrado');
            return $this -> redirectToRoute('cow_index');
        }

        return $this -> render('cow/form.html.twig', [
            'form' => $form,
            'title' => 'Novo animal',
        ]);

    }

    #[Route('/{id}/editar', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Cow $cow, Request $request, EntityManagerInterface $em): Response
    {
        if($cow -> isAbatido()){
            $this -> addFlash('warning', 'Animais abatidos não pode ser editado');
            return $this -> redirectToRoute('cow_index');
        }

        $form = $this ->createForm(CowType::class, $cow);
        $form ->handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()){
            $em -> flush();
            $this ->addFlash('success', 'Animal atualizado');
            return $this -> redirectToRoute('cow_index');
        }

        return $this -> render('cow/form.html.twig', [
            'form' => $form,
            'title' => 'Editar animal',
            'cow' => $cow,
        ]);
    }

    #[Route('/{id}/excluir', name:'delete', methods:['POST'])]
    public function delete(Cow $cow, Request $request, EntityManagerInterface $em) :Response
    {
        if($this -> isCsrfTokenValid('delete-cow-' . $cow -> getId(), $request -> get('_token'))) {
            $em -> remove($cow);
            $em -> flush();
            $this -> addFlash('success', 'Animal excluído');
        }

        return $this -> redirectToRoute('cow_index');
    }
}
