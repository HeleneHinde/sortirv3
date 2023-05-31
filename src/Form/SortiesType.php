<?php

namespace App\Form;

use App\Entity\Sortie;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('firstAirDate', DateType::class,[
                'html5'=>true,
                'widget'=>'single_text'
            ])

            ->add('dateLimiteInscription', DateType::class,[
                'html5'=>true,
                'widget'=>'single_text'
            ])
            ->add('nbInscriptionMax')
            ->add('duree')
            ->add('infosSortie')
            //->add('users')
           // ->add('etat')
            //->add('campus',[
              //  'mapped'=> false])
            //->add('user')
            //->add('lieu', EntityType::class)
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
