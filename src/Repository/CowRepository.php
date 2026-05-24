<?php

namespace App\Repository;

use App\Entity\Cow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cow::class);
    }

    //busca de todos os aniamis vivos
    public function findAllAlive(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.farm', 'f')
            ->addSelect('f')
            ->where('c.slaughtered = false')
            ->orderBy('f.name', 'ASC')
            ->addOrderBy('c.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //metodo do qb para listar com filtro opcional de fazenda
    public function createListQueryBuilder(?int $farmId = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.slaughtered = false')
            ->orderBy('c.code', 'ASC');

        if ($farmId) {
            $qb->join('c.farm', 'f')
                ->andWhere('f.id = :farmId')
                ->setParameter('farmId', $farmId);
        }
        return $qb;
    }

    //busca de animais abatidos
    public function findAllSlaughtered(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.farm', 'f')
            ->addSelect('f')
            ->where('c.slaughtered = true')
            ->orderBy('c.slaughteredAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

   //total de leite por semana
    public function getTotalMilkPerWeek(): float
    {
        return (float) $this->createQueryBuilder('c')
            ->select('SUM(c.milkLitersPerWeek)')
            ->where('c.slaughtered = false')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    //total de racao por semana
    public function getTotalRationPerWeek(): float
    {
        return (float) $this->createQueryBuilder('c')
            ->select('SUM(c.rationKgPerWeek)')
            ->where('c.slaughtered = false')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    //animal jovem com 1 ano e que tenha consumo mais que 500kg/semana
    public function findYoungHeavyEaters(): array
    {
        $oneYearAgo = new \DateTime('-1 year');

        return $this->createQueryBuilder('c')
            ->where('c.slaughtered = false')
            ->andWhere('c.birthDate >= :oneYearAgo')
            ->andWhere('c.rationKgPerWeek > :consumption')
            ->setParameter('oneYearAgo', $oneYearAgo)
            ->setParameter('consumption', 500)
            ->orderBy('c.birthDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

   //verifica animal com o memso cod excluindo id especifico na hora da edicao
    public function findLiveByCode(string $code, ?int $excludeId = null): ?Cow
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.code = :code')
            ->andWhere('c.slaughtered = false')
            ->setParameter('code', strtoupper(trim($code)));

        if ($excludeId) {
            $qb->andWhere('c.id != :id')->setParameter('id', $excludeId);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }


}