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
    private ?string $responsible = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'O tamanho é obrigatório.')]
    #[Assert\Positive(message: 'O tamanho deve ser maior que zero.')]
    private ?string $size = null;

    #[ORM\ManyToMany(targetEntity: Veterinarian::class, inversedBy: 'farms')]
    private Collection $veterinarians;

    #[ORM\OneToMany(targetEntity: Cow::class, mappedBy: 'farm')]
    private Collection $cows;

    public function __construct()
    {
        $this->veterinarians = new ArrayCollection();
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

    public function getResponsible(): ?string
    {
        return $this->responsible;
    }

    public function setResponsible(string $responsible): static
    {
        $this->responsible = $responsible;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getVeterinarians(): Collection
    {
        return $this->veterinarians;
    }

    public function addVeterinarian(Veterinarian $veterinarian): static
    {
        if (!$this->veterinarians->contains($veterinarian)) {
            $this->veterinarians->add($veterinarian);
        }

        return $this;
    }

    public function removeVeterinarian(Veterinarian $veterinarian): static
    {
        $this->veterinarians->removeElement($veterinarian);

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
            $cow->setFarm($this);
        }

        return $this;
    }

    public function removeCow(Cow $cow): static
    {
        if ($this->cows->removeElement($cow)) {
            if ($cow->getFarm() === $this) {
                $cow->setFarm(null);
            }
        }

        return $this;
    }


    //Regras
    //quantidade de animal e que esteja vivo
    public function getSizeFloat(): float{
        return (float) $this->size;
    }

    //max de 18 animal por hectare
    public function getMaxCapacity():int{
        return (int) floor($this -> getSizeFloat() * 18);
    }

    //retorna apenas animais vivos
    public function getLiveAnimals(): Collection{
        return $this->cows->filter(fn(Cow $c) => !$c->isSlaughtered());
    }

    //Ve se a fazenda tem capacidade
    public function hasCapacity(): bool{
        return  $this->getLiveAnimals()->count() < $this->getMaxCapacity();
    }
}
