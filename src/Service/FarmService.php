<?php

namespace App\Service;

use App\Entity\Farm;
use App\Repository\FarmRepository;
use Doctrine\ORM\EntityManagerInterface;

class FarmService
{
    public function __construct(
        private FarmRepository $farmRepo,
        private EntityManagerInterface $em,
    ) {}

    //valida e cadastra fazenda
    public function create(Farm $farm): ?string
    {
        $error = $this->validateUniqueName($farm->getName());
        if($error){
            return $error;
        }

        $this->em->persist($farm);
        $this->em->flush();
        return null;
    }


    //valida e att fazenda
    public function update(Farm $farm): ?string
    {
       $existing = $this->farmRepo->createQueryBuilder('f')
           ->where('f.name =: name')
           ->andWhere('f.id != id')
           ->setParameter('name', $farm->getName())
           ->setParameter('id', $farm->getId())
           ->getQuery()
           ->getOneOrNullResult();

       if ($existing){
           return "Já existe uma fazenda com o nome \"{$farm->getName()}\".";
       }

       $this->em->flush();
       return null;
    }

    //valida e remove farm
    public function delete(Farm $farm): ?string
    {
        if($farm->getAnimaisVivos()->count()>0){
            return "Não é possível remover a fazenda \"{$farm->getName()}\", ela possui animais ";
        }
        $this->em->remove($farm);
        $this->em->flush();
        return null;
    }

    private function validateUniqueName(string $name): ?string
    {
        $existing = $this->farmRepo->findExactName($name);
        if ($existing){
            return "Já existe uma fazenda com o nome \"{$name}\".";
        }
        return null;
    }

}