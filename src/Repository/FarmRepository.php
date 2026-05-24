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


    //qb para listagem
    public function createListQueryBuilder(?string $search = null)
    {
        $qb = $this->createQueryBuilder('f')
            ->orderBy('f.name', 'ASC');

        if ($search) {
            $qb->where('LOWER(f.name) LIKE LOWER(:term)')
                ->orWhere('LOWER(f.responsible) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $search . '%');
        }

        return $qb;
    }

    //busca fazenda por nome excluindo um id especifivo na edicao para verificar o mesmo nome
    public function findByNameExcluding(string $name, int $excludeId): ?Farm
    {
        return $this->createQueryBuilder('f')
            ->where('f.name = :name')
            ->andWhere('f.id != :id')
            ->setParameter('name', $name)
            ->setParameter('id', $excludeId)
            ->getQuery()
            ->getOneOrNullResult();
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


}
