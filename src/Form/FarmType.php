<?php

namespace App\Form;

use App\Entity\Farm;
use App\Entity\Veterinarian;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nome da fazenda'],
            ])
            ->add('responsible', TextType::class, [
                'label' => 'Responsável',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nome do responsável'],
            ])
            ->add('size', NumberType::class ,[
                'label' => 'Tamanho(hectares)',
                'scale' => 2,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 150.50'],
            ])
            ->add('veterinarians', EntityType::class, [
                'class' => Veterinarian::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Veterinários',
                'required' => false,
                'attr' => ['class' => 'form-control', 'size' => 5],
                'help' => 'Segure Ctrl para selecionar mais de um',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Farm::class,
        ]);
    }
}
