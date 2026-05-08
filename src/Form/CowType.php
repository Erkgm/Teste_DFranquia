<?php

namespace App\Form;

use App\Entity\Cow;
use App\Entity\Farm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo')
            ->add('litrosLeitePorSemana')
            ->add('racaoPorSemana')
            ->add('peso')
            ->add('dataNascimento')
            ->add('abatido')
            ->add('abatidoEm')
            ->add('fazenda', EntityType::class, [
                'class' => Farm::class,
                'choice_label' => 'id',
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
