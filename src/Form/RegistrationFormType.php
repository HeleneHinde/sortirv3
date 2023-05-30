<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Serie;
use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\SerieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label'=>'Pseudo'
            ])
            ->add('firstname', TextType::class, [
                'label'=>'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'label'=>'Nom'
            ])
            ->add('phoneNumber', TextType::class, [
                'label'=>'Numéro de téléphone'
            ])
            ->add('phoneNumber', TextType::class, [
                'label'=>'Numéro de téléphone'
            ])
            ->add('email', TextType::class, [
                'label'=>'E-Mail'
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label'=>'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label'=>'Confirmation de mot de passe',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a confirmation of password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('campus', EntityType::class, [
                //quelle entité est liée
                'class'=>Campus::class,
                //quel attribut servira a afficher l'information
                'choice_label'=>'name',
                'query_builder'=> function(CampusRepository $campusRepository){
                    $qb= $campusRepository->createQueryBuilder('c');
                    $qb->addOrderBy('c.name', 'ASC');
                    return $qb;
                }
            ])

            ->add('photo', FileType::class, [
                'mapped'=>false,
                'label'=>'Photo de Profil',
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
