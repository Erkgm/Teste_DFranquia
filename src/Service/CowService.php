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
    public function getMotivoAbate(Cow $cow): array{
        $motivos = [];

        if($this->isIdadeExcedida($cow)){
            $motivos[] = 'Idade maior de 5 anos (' . number_format($cow->getIdadeEmAnos(), 1, ',', '.') . ')';
        }
        if ($this->isProducaoLeiteBaixa($cow)) {
            $motivos[] = 'Produção de leite abaixo de 40L/semana (' . $cow->getLeiteFloat() . 'L)';
        }
        if ($this->isIneficiente($cow)) {
            $motivos[] = 'Produção menos de 70L/semana e consumo de ração mais 50kg/dia (' . number_format($cow->getRacaoPorDia(), 1, ',', '.') . 'kg/dia)';
        }
        if ($this->isPesoExcedido($cow)) {
            $motivos[] = 'Peso superior a 18 arrobas (' . number_format($cow->getPesoEmArrobas(), 2, ',', '.') . (')');
        }
        return $motivos;
    }


    //Regras de Abate

    //Caso tenha mais de 5 anos
    public function isIdadeExcedida(Cow $cow): bool
    {
        return $cow->getIdadeEmAnos() > Cow::MAX_IDADE_ANOS;
    }

    //Caso tenha produção de menos 40L por semana
    public function isProducaoLeiteBaixa(Cow $cow): bool
    {
        return $cow->getLeiteFloat() < Cow::MIN_LEITE_SEMANA;
    }

    //Caso a produção tenha menos de 70L/semana e mais de 50kg de ração/dia
    public function isIneficiente(Cow $cow): bool
    {
        return $cow->getLeiteFloat() < Cow::MIN_LEITE_EFICIENCIA
            && $cow->getRacaoPorDia() > Cow::MAX_RACAO_DIA;
    }
    //Caso tenha mais de 18 arrobas
    public function isPesoExcedido(Cow $cow): bool
    {
        return $cow->getPesoEmArrobas() > Cow::MAX_PESO_ARROBAS;
    }

    public function podeSerAbatido(Cow $cow): bool
    {
        return $this->isIdadeExcedida($cow)
            || $this->isProducaoLeiteBaixa($cow)
            || $this->isIneficiente($cow)
            || $this->isPesoExcedido($cow);
    }

    //CRUD
    //valida e cadastra aimal
    public function create(Cow $cow): ?string
    {
        try{
            $existing = $this->cowRepo->findLiveByCode($cow->getCodigo());
            if ($existing) {
                return "Já existe um animal vivo com o código \"{$cow->getCodigo()}\".";
            }

            $error = $this->validateFarmCapacity($cow->getFazenda());
            if ($error) {
                return $error;
            }

            $cow->setAbatido(false);
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
            $existing = $this->cowRepo->findLiveByCode($cow->getCodigo(), $cow->getId());
            if ($existing) {
                return "Já existe um animal vivo com o código \"{$cow->getCodigo()}\"";
            }

            $fazenda = $cow->getFazenda();
            if ($oldFarmId && $fazenda->getId() !== $oldFarmId) {
                if (!$fazenda->temCapacidade()) {
                    return "A fazenda \"{$fazenda->getName()}\" atingiu a capacidade máxima de {$fazenda->getCapacidadeMaxima()} animais";
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
            if ($cow->isAbatido()) {
                return "O animal \"{$cow->getCodigo()}\" já foi abatido.";
            }

            if (!$this->podeSerAbatido($cow)) {
                return "O animal \"{$cow->getCodigo()}\" não atende às condições de abate.";
            }

            $cow->setAbatido(true);
            $cow->setAbatidoEm(new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')));
            $this->em->flush();

            return null;
        } catch (\Exception $e) {
            return "Erro ao realizar abate: " . $e->getMessage();
        }

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