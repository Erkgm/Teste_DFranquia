<?php

namespace App\Repository;

use App\Entity\Farm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Farm>
 */
class FarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Farm::class);
    }

    //busca de todas as fazendas ordenadas
   public function findAllOrderByName(): array
   {
       return $this->createQueryBuilder('f')
           ->orderBy('f.name', 'ASC')
           ->getQuery()
           ->getResult();
   }

   //busca as fazendas com os veterinario
   public function findAllWithVet():array
   {
       return $this->createQueryBuilder('f')
           ->leftJoin('f.veterinarios', 'v')
           ->addSelect('v')
           ->orderBy('f.name', 'ASC')
           ->getQuery()
           ->getResult();
   }

   //busca de fazenda com capacidade disponivel
   public function findFarmWithCap(): array
   {
       $farms = $this->findAllWithVet();
       return array_filter($farms, fn(Farm $f) => $f->temCapacidade());
   }

   //busca pelo nome exato da fazenda
    public function findExactName(string $name): ?Farm
    {
        return $this->createQueryBuilder('f')
            ->where('f.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNameOrResponsavel(string $term): array
    {
        return $this->createQueryBuilder('f')
            ->where('LOWER(f.name) LIKE LOWER(:term)')
            ->orWhere('LOWER(f.responsavel) LIKE LOWER(:term)')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
