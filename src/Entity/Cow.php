<?php

namespace App\Entity;

use App\Repository\CowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CowRepository::class)]
class Cow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $codigo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $litrosLeitePorSemana = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $racaoPorSemana = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $peso = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dataNascimento = null;

    #[ORM\Column]
    private ?bool $abatido = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $abatidoEm = null;

    #[ORM\ManyToOne(inversedBy: 'cows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Farm $fazenda = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getLitrosLeitePorSemana(): ?string
    {
        return $this->litrosLeitePorSemana;
    }

    public function setLitrosLeitePorSemana(string $litrosLeitePorSemana): static
    {
        $this->litrosLeitePorSemana = $litrosLeitePorSemana;

        return $this;
    }

    public function getRacaoPorSemana(): ?string
    {
        return $this->racaoPorSemana;
    }

    public function setRacaoPorSemana(string $racaoPorSemana): static
    {
        $this->racaoPorSemana = $racaoPorSemana;

        return $this;
    }

    public function getPeso(): ?string
    {
        return $this->peso;
    }

    public function setPeso(string $peso): static
    {
        $this->peso = $peso;

        return $this;
    }

    public function getDataNascimento(): ?\DateTime
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(\DateTime $dataNascimento): static
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    public function isAbatido(): ?bool
    {
        return $this->abatido;
    }

    public function setAbatido(bool $abatido): static
    {
        $this->abatido = $abatido;

        return $this;
    }

    public function getAbatidoEm(): ?\DateTime
    {
        return $this->abatidoEm;
    }

    public function setAbatidoEm(?\DateTime $abatidoEm): static
    {
        $this->abatidoEm = $abatidoEm;

        return $this;
    }

    public function getFazenda(): ?Farm
    {
        return $this->fazenda;
    }

    public function setFazenda(?Farm $fazenda): static
    {
        $this->fazenda = $fazenda;

        return $this;
    }
}
