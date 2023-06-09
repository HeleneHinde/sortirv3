<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;

use App\Entity\Ville;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TimeType;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class SortiesType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('firstAirDate', DateTimeType::class,[
                'label' => 'Date et heure de la sortie',
                'html5'=>true,
                'widget'=>'single_text',
                'required' => true
            ])

            ->add('dateLimiteInscription', DateTimeType::class,[
                'label' => 'Date de fin d\'inscription',
                'html5'=>true,
                'widget'=>'single_text',
                'required' => true
            ])
            ->add('nbInscriptionMax', NumberType::class,[
                'label' => 'Max. inscrit',
                'required' => true,
                'invalid_message' => 'Le nombre d\'inscription doit être un nombre'
            ])
            ->add('duree', \Symfony\Component\Form\Extension\Core\Type\TimeType::class, [
                'label' => 'Durée',
                'required' => true
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Informations',
                'required' => true
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'label' => 'Lieu',
                'choice_label' => 'nom',
                'choice_value' => 'id'
            ])
            ->add('nom_lieu', TextType::class, [
                'mapped' => false,
                'label' => 'nom',
                'disabled' => true
            ])
            ->add('Latitude', TextType::class, [
                'mapped' => false,
                'disabled' => true
            ])
            ->add('Longitude', TextType::class, [
                'mapped' => false,
                'disabled' => true
            ])
            ->add('ville_select', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'disabled' => true,
                'label' => 'Ville',
                'choice_label' => 'name',
                'choice_value' => 'id'
            ])
            ->add('rue', TextType::class, [
                'mapped' => false,
                'disabled' => true
            ])
            //->add('users')
           // ->add('etat')
           // ->add('campus',[
             //   'mapped'=> false])
            //->add('user')
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
