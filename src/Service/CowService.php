<?php

namespace App\Service;

use App\Entity\Cow;
use App\Entity\Farm;
use App\Repository\CowRepository;
use Doctrine\ORM\EntityManagerInterface;

class CowService
{
    public function __construct(
        private CowRepository $cowRepo,
        private EntityManagerInterface $em,
    ) {}

    //valida e cadastra aimal
    public function create(Cow $cow): ?string
    {
        // Valida cod unico
        $existing = $this->cowRepo->findAnimalPorCod($cow->getCodigo());
        if ($existing) {
            return "Já existe um animal vivo com o código \"{$cow->getCodigo()}\".";
        }

        // Valida capacidade da fazenda
        $error = $this->validateFarmCapacity($cow->getFazenda());
        if ($error) {
            return $error;
        }

        $cow->setAbatido(false);
        $this->em->persist($cow);
        $this->em->flush();

        return null;
    }

   //valida e att animal
    public function update(Cow $cow): ?string
    {
        $existing = $this->cowRepo->findAnimalPorCod($cow->getCodigo(), $cow->getId());
        if ($existing) {
            return "Já existe um animal vivo com o código \"{$cow->getCodigo()}\".";
        }

        $this->em->flush();
        return null;
    }

    //valida e abate animal
    public function slaughter(Cow $cow): ?string
    {
        if ($cow->isAbatido()) {
            return "O animal \"{$cow->getCodigo()}\" já foi abatido.";
        }

        if (!$cow->podeSerAbatido()) {
            return "O animal \"{$cow->getCodigo()}\" não atende às condições de abate.";
        }

        $cow->setAbatido(true);
        $cow->setAbatidoEm(new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')));
        $this->em->flush();

        return null;
    }

    //valida a cap da fazenda
    private function validateFarmCapacity(Farm $farm): ?string
    {
        if (!$farm->temCapacidade()) {
            return "A fazenda \"{$farm->getName()}\" atingiu a capacidade máxima de {$farm->getCapacidadeMaxima()} animais.";
        }
        return null;
    }
}