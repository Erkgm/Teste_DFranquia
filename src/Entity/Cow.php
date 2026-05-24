<?php

namespace App\Entity;

use App\Repository\CowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: CowRepository::class)]
class Cow
{
    public const ARROBA_KG = 15;
    public const MAX_AGE_YEARS = 5;
    public const MIN_MILK_PER_WEEK = 40;
    public const MIN_MILK_EFFICIENCY = 70;
    public const MAX_RATION_PER_DAY = 50;
    public const MAX_WEIGHT_ARROBAS = 18;


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Código obrigatório')]
    private ?string $code = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'Produção do leite é obrigatório')]
    #[Assert\PositiveOrZero(message: 'A quantidade não pode ser negatica')]
    private ?string $milkLitersPerWeek = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'Quantidade de ração é obrigatório')]
    #[Assert\PositiveOrZero(message: 'A quantidade não pode ser negativa')]
    private ?string $rationKgPerWeek = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'Peso é obrigatório')]
    #[Assert\PositiveOrZero(message: 'O peso não pode ser abaixo de 0')]
    private ?string $weightKg = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Data de nascimento é obrigatório')]
    private ?\DateTime $birthDate = null;

    #[ORM\Column]
    private ?bool $slaughtered = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $slaughteredAt = null;

    #[ORM\ManyToOne(inversedBy: 'cows')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'FAzenda é obrigatório')]
    private ?Farm $farm = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = strtoupper(trim($code));
        return $this;
    }

    public function getMilkLitersPerWeek(): ?string
    {
        return $this->milkLitersPerWeek;
    }

    public function setMilkLitersPerWeek(string $v): static
    {
        $this->milkLitersPerWeek = $v;
        return $this;
    }

    public function getRationKgPerWeek(): ?string
    {
        return $this->rationKgPerWeek;
    }

    public function setRationKgPerWeek(string $v): static
    {
        $this->rationKgPerWeek = $v;
        return $this;
    }

    public function getWeightKg(): ?string
    {
        return $this->weightKg;
    }

    public function setWeightKg(string $v): static
    {
        $this->weightKg = $v;

        return $this;
    }

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTime $d): static
    {
        $this->birthDate = $d;
        return $this;
    }

    public function isSlaughtered(): ?bool
    {
        return $this->slaughtered;
    }

    public function setSlaughtered(bool $v): static
    {
        $this->slaughtered = $v;

        return $this;
    }

    public function getSlaughteredAt(): ?\DateTime
    {
        return $this->slaughteredAt;
    }

    public function setSlaughteredAt(?\DateTime $v): static
    {
        $this->slaughteredAt = $v;

        return $this;
    }

    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    public function setFarm(?Farm $farm): static
    {
        $this->farm = $farm;

        return $this;
    }

    //validação para a datga de nascimento
    #[Assert\Callback]
    public function validateBirthDate(ExecutionContextInterface $context): void
    {
        if ($this->birthDate && $this->birthDate > new \DateTime()) {
            $context->buildViolation('A data de nascimento não pode ser futura.')
                ->atPath('birthDate')
                ->addViolation();
        }
    }

    //Conversões e validacao
    public function getWeightFloat():float{
        return (float) $this ->weightKg;
    }

    public function getMilkFloat():float{
        return(float) $this->milkLitersPerWeek;
    }

    public function getRacaoFloat():float{
        return (float) $this->rationKgPerWeek;
    }

    public function getWeightInArrobas():float{
        return $this ->getWeightFloat() / self::ARROBA_KG;
    }

    public function getRationPerDay():float{
        return $this ->getRacaoFloat() / 7;
    }

    public function getAgeInYears():float{
        if(!$this->birthDate) return 0;
        $diff = (new \DateTime()) -> diff($this->birthDate);
        return $diff->y + ($diff->m / 12);
    }
}
