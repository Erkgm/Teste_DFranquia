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
    public function findTodosVivos(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.fazenda', 'f')
            ->addSelect('f')
            ->where('c.abatido = false')
            ->orderBy('f.name', 'ASC')
            ->addOrderBy('c.codigo', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //animais vivos por fazenda
    public function findVivosPorFarm(int $farmId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.fazenda', 'f')
            ->where('c.abatido = false')
            ->andWhere('f.id = :farmId')
            ->setParameter('farmId', $farmId)
            ->orderBy('c.codigo', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //busca de animais prontos para abate
    public function findProntosParaAbate(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.fazenda', 'f')
            ->addSelect('f')
            ->where('c.abatido = false')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //busca de animais abatidos
    public function findTodosAbatidos(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.fazenda', 'f')
            ->addSelect('f')
            ->where('c.abatido = true')
            ->orderBy('c.abatidoEm', 'DESC')
            ->getQuery()
            ->getResult();
    }

   //total de leite por semana
    public function getTotalLeitePorSemana(): float
    {
        return (float) $this->createQueryBuilder('c')
            ->select('SUM(c.litrosLeitePorSemana)')
            ->where('c.abatido = false')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    //total de racao por semana
    public function getTotalRacaoPorSemana(): float
    {
        return (float) $this->createQueryBuilder('c')
            ->select('SUM(c.racaoPorSemana)')
            ->where('c.abatido = false')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    //animal jovem com 1 ano e que tenha consumo mais que 500kg/semana
    public function findJovemAltoConsumo(): array
    {
        $umAnoAtras = new \DateTime('-1 year');

        return $this->createQueryBuilder('c')
            ->where('c.abatido = false')
            ->andWhere('c.dataNascimento >= :umAnoAtras')
            ->andWhere('c.racaoPorSemana > :consumo')
            ->setParameter('umAnoAtras', $umAnoAtras)
            ->setParameter('consumo', 500)
            ->orderBy('c.dataNascimento', 'DESC')
            ->getQuery()
            ->getResult();
    }

   //verifica animal com o memso cod
    public function findAnimalPorCod(string $codigo, ?int $excludeId = null): ?Cow
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.codigo = :codigo')
            ->andWhere('c.abatido = false')
            ->setParameter('codigo', strtoupper(trim($codigo)));

        if ($excludeId) {
            $qb->andWhere('c.id != :id')->setParameter('id', $excludeId);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}