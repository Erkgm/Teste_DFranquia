<?php

namespace App\Form;

use App\Entity\Cow;
use App\Entity\Farm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo', TextType::class, [
                'label' => 'Código',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: BOV-001'],
            ])
            ->add('litrosLeitePorSemana', NumberType::class, [
                'label' => 'Leite (litros/semana)',
                'scale' => 2,
                'attr' => ['class' => 'form-control', 'placeholder' => '0.00'],
            ])
            ->add('racaoPorSemana', NumberType::class, [
                'label' => 'Ração(kg/semana)',
                'scale' => 2,
                'attr' => ['class' => 'form-control', 'placeholder' => '0.00'],
            ])
            ->add('peso', NumberType::class,[
                'label' => 'Peso(kg)',
                'scale' => 2,
                'attr' => ['class' => 'form-control', 'placeholder' => '0.00'],
            ])
            ->add('dataNascimento', DateType::class, [
                'label' => 'Data de Nascimento',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('fazenda', EntityType::class, [
                'class' => Farm::class,
                'choice_label' => 'name',
                'label' => 'Fazenda',
                'placeholder' => 'Selecione a fazenda',
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cow::class,
        ]);
    }
}
