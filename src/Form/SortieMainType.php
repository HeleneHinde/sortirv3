<?php

namespace App\Form;

use App\Entity\Campus;

use App\Entity\Sortie;

use App\Repository\CampusRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieMainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'mapped'=>false,
                'required' => false,
                'label'=>'Le nom de la sortie contient :'

            ])
            ->add('dateUn', DateType::class, [
                'label'=>'Entre le : ',
                'required' => false,
                'mapped'=>false,
                'html5'=>true,
                'widget'=>'single_text'

            ])
            ->add('dateDeux', DateType::class, [
                'label'=>'Et le : ',
                'required' => false,
                'mapped'=>false,
                'html5'=>true,
                'widget'=>'single_text'

            ])
            ->add('campus', EntityType::class, [
                //quelle entité est liée
                'class'=>Campus::class,
                //quel attribut servira a afficher l'information
                'choice_label'=>'name',
                'query_builder'=> function(CampusRepository $campusRepository){
                    $qb= $campusRepository->createQueryBuilder('s');
                    $qb->addOrderBy('s.name', 'ASC');
                    return $qb;
                },
                'placeholder' => 'Sélectionnez un campus', // Ajouter cette ligne
                'required' => false,
                ])
            ->add('scales', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/organisatrice",
                'required' => false,
                'mapped'=>false

            ])
            //champ caché pour récupérer l'id de celui qui effectue la recherche
            ->add('userId', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('horns', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit(e)',
                'required' => false,
                'mapped'=>false
            ])
            ->add('horns_not_registered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e)',
                'required' => false,
                'mapped'=>false
            ])
            ->add('past_outings', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
                'mapped'=>false
            ]);


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
