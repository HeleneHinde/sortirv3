<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('latitude', NumberType::class, [
                'attr' => [
                    'hidden' => true
                ]
            ])
            ->add('longitude', NumberType::class, [
                'attr' => [
                    'hidden' => true
                ]
            ])
            ->add('rue')
            ->add('ville', EntityType::class, [
                'label' => 'Ville',
                'class' => Ville::class,
                'choice_label' => 'name',
                'choice_value' => 'id'
            ])
            ->add('Ville', TextType::class, [
                'mapped' => false,
                'disabled' => true
            ])
            ->add('Code_postal', NumberType::class, [
                'mapped' => false,
                'label' => 'Code postal',
                'disabled' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
