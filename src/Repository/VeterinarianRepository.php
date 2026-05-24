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

    //qb para listagem
    public function createListQueryBuilder(?string $search = null)
    {
        $qb = $this->createQueryBuilder('v')
            ->orderBy('v.name', 'ASC');

        if ($search) {
            $qb->where('LOWER(v.name) LIKE LOWER(:term)')
                ->orWhere('LOWER(v.crmv) LIKE LOWER(:term)')
                ->setParameter('term', '%' . $search . '%');
        }

        return $qb;
    }

    //busca vet por crmv excluindo um id especifico, para na edicao verificar se é unico
    public function findByCrmvExcluding(string $crmv, int $excludeId): ?Veterinarian
    {
        return $this->createQueryBuilder('v')
            ->where('v.crmv = :crmv')
            ->andWhere('v.id != :id')
            ->setParameter('crmv', strtoupper(trim($crmv)))
            ->setParameter('id', $excludeId)
            ->getQuery()
            ->getOneOrNullResult();
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


}
