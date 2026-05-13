<?php

namespace App\Repository;

use App\Entity\Veterinarian;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Veterinarian>
 */
class VeterinarianRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Veterinarian::class);
    }

    //busca todos vet por ordem
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //busca por cmrv
    public function findByCrmv(string $crmv): ?Veterinarian
    {
        return $this->createQueryBuilder('v')
            ->where('v.crmv = :crmv')
            ->setParameter('crmv', strtoupper(trim($crmv)))
            ->getQuery()
            ->getOneOrNullResult();
    }

    //busca por nome ou crmv
    public function findByNameOrCrmv(string $term): array
    {
        return $this->createQueryBuilder('v')
            ->where('LOWER(v.name) LIKE LOWER(:term)')
            ->orWhere('LOWER(v.crmv) LIKE LOWER(:term)')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('v.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //busca todos com ligados a fazenda
    public function findAllWithFarms(): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.farms', 'f')
            ->addSelect('f')
            ->orderBy('v.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
