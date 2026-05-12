<?php

namespace App\Entity;

use App\Repository\FarmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FarmRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Já existe uma fazenda com esse nome')]
class Farm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Informe o nome da fazenda')]
    private ?string $name = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Informe o responsável da fazenda')]
    private ?string $responsavel = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'O tamanho é obrigatório.')]
    #[Assert\Positive(message: 'O tamanho deve ser maior que zero.')]
    private ?string $tamanho = null;

    #[ORM\ManyToMany(targetEntity: Veterinarian::class, inversedBy: 'farms')]
    private Collection $veterinarios;

    #[ORM\OneToMany(targetEntity: Cow::class, mappedBy: 'fazenda')]
    private Collection $cows;

    public function __construct()
    {
        $this->veterinarios = new ArrayCollection();
        $this->cows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getResponsavel(): ?string
    {
        return $this->responsavel;
    }

    public function setResponsavel(string $responsavel): static
    {
        $this->responsavel = $responsavel;

        return $this;
    }

    public function getTamanho(): ?string
    {
        return $this->tamanho;
    }

    public function setTamanho(string $tamanho): static
    {
        $this->tamanho = $tamanho;

        return $this;
    }

    public function getVeterinarios(): Collection
    {
        return $this->veterinarios;
    }

    public function addVeterinario(Veterinarian $veterinario): static
    {
        if (!$this->veterinarios->contains($veterinario)) {
            $this->veterinarios->add($veterinario);
        }

        return $this;
    }

    public function removeVeterinario(Veterinarian $veterinario): static
    {
        $this->veterinarios->removeElement($veterinario);

        return $this;
    }

    public function getCows(): Collection
    {
        return $this->cows;
    }

    public function addCow(Cow $cow): static
    {
        if (!$this->cows->contains($cow)) {
            $this->cows->add($cow);
            $cow->setFazenda($this);
        }

        return $this;
    }

    public function removeCow(Cow $cow): static
    {
        if ($this->cows->removeElement($cow)) {
            if ($cow->getFazenda() === $this) {
                $cow->setFazenda(null);
            }
        }

        return $this;
    }


    //Regras
    //quantidade de animal e que esteja vivo
    public function getTamanhoFloat(): float{
        return (float) $this->tamanho;
    }

    //max de 18 animal por hectare
    public function getCapacidadeMaxima():int{
        return (int) floor($this -> getTamanhoFloat() * 18);
    }

    //retorna apenas animais vivos
    public function getAnimaisVivos(): Collection{
        return $this->cows->filter(fn(Cow $c) => !$c->isAbatido());
    }

    //Ve se a fazenda tem capacidade
    public function temCapacidade(): bool{
        return  $this->getAnimaisVivos()->count() < $this->getCapacidadeMaxima();
    }
}
