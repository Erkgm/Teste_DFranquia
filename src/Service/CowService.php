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

    //Verifica animal para o abate
    public function getSlaughterReasons(Cow $cow): array{
        $reasons  = [];

        if($this->isOverAge($cow)){
            $reasons [] = 'Idade maior de 5 anos (' . number_format($cow->getAgeInYears(), 1, ',', '.') . ')';
        }
        if ($this->isLowMilkProducer($cow)) {
            $reasons [] = 'Produção de leite abaixo de 40L/semana (' . $cow->getMilkFloat() . 'L)';
        }
        if ($this->isInefficient($cow)) {
            $reasons [] = 'Produção menos de 70L/semana e consumo de ração mais 50kg/dia (' . number_format($cow->getRationPerDay(), 1, ',', '.') . 'kg/dia)';
        }
        if ($this->isOverweight($cow)) {
            $reasons [] = 'Peso superior a 18 arrobas (' . number_format($cow->getWeightInArrobas(), 2, ',', '.') . (')');
        }
        return $reasons ;
    }


    //Regras de Abate

    //Caso tenha mais de 5 anos
    public function isOverAge(Cow $cow): bool
    {
        return $cow->getAgeInYears() > Cow::MAX_AGE_YEARS;
    }

    //Caso tenha produção de menos 40L por semana
    public function isLowMilkProducer(Cow $cow): bool
    {
        return $cow->getMilkFloat() < Cow::MIN_MILK_PER_WEEK;
    }

    //Caso a produção tenha menos de 70L/semana e mais de 50kg de ração/dia
    public function isInefficient(Cow $cow): bool
    {
        return $cow->getMilkFloat() < Cow::MIN_MILK_EFFICIENCY
            && $cow->getRationPerDay() > Cow::MAX_RATION_PER_DAY;
    }
    //Caso tenha mais de 18 arrobas
    public function isOverweight(Cow $cow): bool
    {
        return $cow->getWeightInArrobas() > Cow::MAX_WEIGHT_ARROBAS;
    }

    public function canBeSlaughtered(Cow $cow): bool
    {
        return $this->isOverAge($cow)
            || $this->isLowMilkProducer($cow)
            || $this->isInefficient($cow)
            || $this->isOverweight($cow);
    }

    //CRUD
    //valida e cadastra aimal
    public function create(Cow $cow): ?string
    {
        try{
            $existing = $this->cowRepo->findLiveByCode($cow->getCode());
            if ($existing) {
                return "Já existe um animal vivo com o código \"{$cow->getCode()}\".";
            }

            $error = $this->validateFarmCapacity($cow->getFarm());
            if ($error) {
                return $error;
            }

            $cow->setSlaughtered(false);
            $this->em->persist($cow);
            $this->em->flush();

            return null;
        } catch (\Exception $e) {
            return "Erro ao cadastrar animal: " . $e->getMessage();
        }

    }

   //valida e att animal
    public function update(Cow $cow, ?int $oldFarmId = null): ?string
    {
        try {
            $existing = $this->cowRepo->findLiveByCode($cow->getCode(), $cow->getId());
            if ($existing) {
                return "Já existe um animal vivo com o código \"{$cow->getCode()}\"";
            }

            $farm = $cow->getFarm();
            if ($oldFarmId && $farm->getId() !== $oldFarmId) {
                if (!$farm->hasCapacity()) {
                    return "A farm \"{$farm->getName()}\" atingiu a capacidade máxima de {$farm->getMaxCapacity()} animais";
                }
            }

            $this->em->flush();
            return null;
        } catch (\Exception $e) {
            return "Erro ao atualizar animal: " . $e->getMessage();
        }
    }

    //valida e abate animal
    public function slaughter(Cow $cow): ?string
    {
        try{
            if ($cow->isSlaughtered()) {
                return "O animal \"{$cow->getCode()}\" já foi abatido.";
            }

            if (!$this->canBeSlaughtered($cow)) {
                return "O animal \"{$cow->getCode()}\" não atende às condições de abate.";
            }

            $cow->setSlaughtered(true);
            $cow->setSlaughteredAt(new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')));
            $this->em->flush();

            return null;
        } catch (\Exception $e) {
            return "Erro ao realizar abate: " . $e->getMessage();
        }

    }

    //valida a cap da fazenda
    private function validateFarmCapacity(Farm $farm): ?string
    {
        if (!$farm->hasCapacity()) {
            return "A fazenda \"{$farm->getName()}\" atingiu a capacidade máxima de {$farm->getMaxCapacity()} animais.";
        }
        return null;
    }


}