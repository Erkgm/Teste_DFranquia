<?php

namespace App\Service;

use App\Entity\Veterinarian;
use App\Repository\VeterinarianRepository;
use Doctrine\ORM\EntityManagerInterface;

class VeterinarianService
{
    public function __construct(
        private VeterinarianRepository $vetRepo,
        private EntityManagerInterface $em,
    ) {}

    //valida e cadastra veterinario
    public function create(Veterinarian $vet): ?string
    {
        $error = $this->validateUniqueCrmv($vet->getCrmv());
        if($error){
            return $error;
        }

        $this->em->persist($vet);
        $this->em->flush();

        return null;
    }

    //valida e att veterinario
    public function update(Veterinarian $vet): ?string
    {
        $existing = $this->vetRepo->findByCrmvExcluding($vet->getCrmv(), $vet->getId());
        if ($existing) {
            return "Já existe um veterinário com o CRMV \"{$vet->getCrmv()}\".";
        }

        $this->em->flush();
        return null;
    }

    //valida e delete veterinario
    public function delete(Veterinarian $vet): ?string
    {
        $this->em->remove($vet);
        $this->em->flush();
        return null;
    }

    public function validateUniqueCrmv(string $crmv): ?string
    {
        $existing = $this->vetRepo->findByCrmv($crmv);
        if ($existing){
            return "Já existe um veterinário com o CRMV \"{$crmv}\"";
        }
        return null;
    }
}