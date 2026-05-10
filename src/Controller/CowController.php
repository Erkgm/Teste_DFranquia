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
    public function new(Request $request, EntityManagerInterface $em, CowRepository $repo): Response
    {
        $cow = new Cow();
        $form = $this ->createForm(CowType::class, $cow);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {
            //ve se animal é codigo unico
            $existing = $repo->findOneBy(['codigo' => $cow->getCodigo(), 'abatido' =>false]);
            if ($existing){
                $this->addFlash('danger', "Já existe um animal com esse codigo \"{$cow->getCodigo()}\".");
                return $$this->render('cow/form.html.twig', ['form' => $form, 'title' => 'Novo Animal']);
            }

            //ve se fazenda tem capacidade
            $fazenda = $cow->getFazenda();
            if (!$fazenda->temCapacidade()){
                $this->addFlash('danger', "A fazenda \"{fazenda->getName()}\" atingiu a capacidade máxima {$fazenda->getCapacidadeMaxima()} animais.");
                return $this->render('cow/form.html.twig', ['form' => $form, 'title' => 'Novo Animal']);
            }
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
    public function edit(Cow $cow, Request $request, EntityManagerInterface $em,  CowRepository $repo): Response
    {
        if($cow -> isAbatido()){
            $this -> addFlash('warning', 'Animais abatidos não pode ser editado');
            return $this -> redirectToRoute('cow_index');
        }

        $form = $this ->createForm(CowType::class, $cow);
        $form ->handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()){
            //valida codigo unico
            $existing = $repo->createQueryBuilder('c')
                ->where('c.codigo = :codigo')
                ->andWhere('c.abitdo = false')
                ->andWhere('c.id != :id')
                ->setParameter('codigo', $cow->getCodigo())
                ->setParameter('id', $cow->getId())
                ->getQuery()
                ->getOneOrNullResult();

            if($existing){
                $this->addFlash('danger', "Já existe um animal vivo com o codigo \"{$cow->getCodigo()}\""."");
                return $this->render('cow/form.html.twig', ['form' => $form, 'title' => 'Editar animal', 'cow' =>$cow]);
            }
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

    #[Route('/lista-abate', name: 'slaughter_list', methods: ['GET'])]
    public function slaughterList(CowRepository $repo): Response
    {
        $animais = $repo->createQueryBuilder('c')
            ->join('c.fazenda', 'f')
            ->addSelect('f')
            ->where('c.abatido = false')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();

        $elegiveis = array_filter($animais, fn(Cow $c) => $c->podeSerAbatido());

        return $this->render('cow/slaughter_list.html.twig', [
            'animais' => $elegiveis,
        ]);
    }

    #[Route('/{id}/abater', name: 'slaughter', methods: ['POST'])]
    public function slaughter(Cow $cow, Request $request, EntityManagerInterface $em):Response
    {
        if(!$this->isCsrfTokenValid('abate-' . $cow->getId(), $request->get('_token'))){
            $this->addFlash('danger', 'Token inválido');
            return $this->redirectToRoute('cow_slaughter_list');
        }

        if(!$cow->podeSerAbatido()){
            $this->addFlash('danger', "O animal \"{$cow->getCodigo()}\"não atande as condições para abate");
            return $this->redirectToRoute('cow_slaughter_list');
        }

        $cow->setAbatido(true);
        $cow->setAbatidoEm(new \DateTime());
        $em -> flush();
        $this->addFlash('success', "Animal \" {$cow->getCodigo()}\"enviado para abate");
        return $this->redirectToRoute('cow_slaughter_list');

    }
}
