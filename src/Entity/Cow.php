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
    public const MAX_IDADE_ANOS = 5;
    public const MIN_LEITE_SEMANA = 40;
    public const MIN_LEITE_EFICIENCIA = 70;
    public const MAX_RACAO_DIA = 50;
    public const MAX_PESO_ARROBAS = 18;


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Código obrigatório')]
    private ?string $codigo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'Produção do leite é obrigatório')]
    #[Assert\PositiveOrZero(message: 'A quantidade não pode ser negatica')]
    private ?string $litrosLeitePorSemana = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'Quantidade de ração é obrigatório')]
    #[Assert\PositiveOrZero(message: 'A quantidade não pode ser negativa')]
    private ?string $racaoPorSemana = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'Peso é obrigatório')]
    #[Assert\PositiveOrZero(message: 'O peso não pode ser abaixo de 0')]
    private ?string $peso = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Data de nascimento é obrigatório')]
    private ?\DateTime $dataNascimento = null;

    #[ORM\Column]
    private ?bool $abatido = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $abatidoEm = null;

    #[ORM\ManyToOne(inversedBy: 'cows')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'FAzenda é obrigatório')]
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
        $this->codigo = strtoupper(trim($codigo));
        return $this;
    }

    public function getLitrosLeitePorSemana(): ?string
    {
        return $this->litrosLeitePorSemana;
    }

    public function setLitrosLeitePorSemana(string $v): static
    {
        $this->litrosLeitePorSemana = $v;
        return $this;
    }

    public function getRacaoPorSemana(): ?string
    {
        return $this->racaoPorSemana;
    }

    public function setRacaoPorSemana(string $v): static
    {
        $this->racaoPorSemana = $v;
        return $this;
    }

    public function getPeso(): ?string
    {
        return $this->peso;
    }

    public function setPeso(string $v): static
    {
        $this->peso = $v;

        return $this;
    }

    public function getDataNascimento(): ?\DateTime
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(\DateTime $d): static
    {
        $this->dataNascimento = $d;
        return $this;
    }

    public function isAbatido(): ?bool
    {
        return $this->abatido;
    }

    public function setAbatido(bool $v): static
    {
        $this->abatido = $v;

        return $this;
    }

    public function getAbatidoEm(): ?\DateTime
    {
        return $this->abatidoEm;
    }

    public function setAbatidoEm(?\DateTime $v): static
    {
        $this->abatidoEm = $v;

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

    //validação para a datga de nascimento
    #[Assert\Callback]
    public function validarDataNascimento(ExecutionContextInterface $context): void
    {
        if ($this->dataNascimento && $this->dataNascimento > new \DateTime()) {
            $context->buildViolation('A data de nascimento não pode ser futura.')
                ->atPath('dataNascimento')
                ->addViolation();
        }
    }

    //Conversões
    public function getPesoFloat():float{
        return (float) $this ->peso;
    }

    public function getLeiteFloat():float{
        return(float) $this->litrosLeitePorSemana;
    }

    public function getRacaoFloat():float{
        return (float) $this->racaoPorSemana;
    }

    public function getPesoEmArrobas():float{
        return $this ->getPesoFloat() / self::ARROBA_KG;
    }

    public function getRacaoPorDia():float{
        return $this ->getRacaoFloat() / 7;
    }

    public function getIdadeEmAnos():float{
        if(!$this->dataNascimento) return 0;
        $diff = (new \DateTime()) -> diff($this->dataNascimento);
        return $diff->y + ($diff->m / 12);
    }
}
