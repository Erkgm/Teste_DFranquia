<?php

namespace App\Entity;

use App\Repository\FarmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FarmRepository::class)]
class Farm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 150)]
    private ?string $responsavel = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $tamanho = null;

    /**
     * @var Collection<int, Veterinarian>
     */
    #[ORM\ManyToMany(targetEntity: Veterinarian::class, inversedBy: 'farms')]
    private Collection $veterinarios;

    /**
     * @var Collection<int, Cow>
     */
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

    /**
     * @return Collection<int, Veterinarian>
     */
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

    /**
     * @return Collection<int, Cow>
     */
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
            // set the owning side to null (unless already changed)
            if ($cow->getFazenda() === $this) {
                $cow->setFazenda(null);
            }
        }

        return $this;
    }
}
