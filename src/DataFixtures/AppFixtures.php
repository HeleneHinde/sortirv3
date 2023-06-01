<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Time;

class AppFixtures extends Fixture

{
    private UserPasswordHasherInterface $hasher;

    private CampusRepository $campusRepository;
    private VilleRepository $villeRepository;
    private EtatRepository $etatRepository;
    private UserRepository $userRepository;
    private LieuRepository $lieuRepository;

    public function __construct(UserPasswordHasherInterface $hasher,
                                CampusRepository $campusRepository,
                                VilleRepository $villeRepository,
                                EtatRepository $etatRepository,
                                UserRepository $userRepository,
                                LieuRepository $lieuRepository)
    {
        $this->hasher=$hasher;
        $this->campusRepository=$campusRepository;
        $this->villeRepository=$villeRepository;
        $this->etatRepository=$etatRepository;
        $this->userRepository=$userRepository;
        $this->lieuRepository=$lieuRepository;

    }

    public function load(ObjectManager $manager): void
    {
//        $this->addUsers($manager, $this->campusRepository);
//        $this->addVille($manager);
//        $this->addLieu($manager, $this->villeRepository);
        $this->addSortie($manager,$this->etatRepository,$this->campusRepository,$this->userRepository,$this->lieuRepository);
    }
    public function addUsers(ObjectManager $manager, CampusRepository $campusRepository):void {


        $generator = Factory::create('fr_FR');

        $campus=$campusRepository->findAll();


        for ($i=0;$i<10;$i++){

            $user = new User();



            $user

                ->setEmail($generator->email)
                ->setFirstname($generator->firstName)
                ->setLastname($generator->lastName)
                ->setRoles(['ROLE_USER'])
                ->setPassword(
                    $this->hasher->hashPassword(
                        $user,
                        '123456'
                    ))
                ->setPhoneNumber($generator->phoneNumber)
                ->setUsername($generator->userName)
                ->setCampus($generator->randomElement($campus));
            $manager->persist($user);

        }
        $userAdmin = new User;

        $userAdmin


            ->setEmail($generator->email)
            ->setFirstname($generator->firstName)
            ->setLastname($generator->lastName)
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(
                $this->hasher->hashPassword(
                    $userAdmin,
                    '123456'
                ))
            ->setPhoneNumber($generator->phoneNumber)
            ->setUsername($generator->userName)
            ->setCampus($generator->randomElement($campus));
        $manager->persist($userAdmin);

        $manager->flush();

    }

    public function addSortie(ObjectManager $manager,
                              EtatRepository $etatRepository,
                              CampusRepository $campusRepository,
                              UserRepository $userRepository,
                              LieuRepository $lieuRepository): void
    {
        $generator = Factory::create('fr_FR');
        $etat = $etatRepository->findAll();
        $campus = $campusRepository->findAll();
        $users = $userRepository->findAll();
        $lieu = $lieuRepository->findAll();


        for ($i = 0 ; $i < 10 ; $i++){
            $sortie = new Sortie();


            $sortie
                ->setEtat($generator->randomElement($etat))
                ->setCampus($generator->randomElement($campus))
                ->setName($generator->userName)
                ->setFirstAirDate($generator->dateTimeBetween('-5 months', 'now'))
                ->setDuree($generator->dateTimeBetween('-3 days', 'now'))
                ->setDateLimiteInscription($generator->dateTimeBetween($sortie->getFirstAirDate(), 'now'))
                ->setNbInscriptionMax($generator->numberBetween('4', '30'))
                ->setInfosSortie($generator->name)
                ->setUser($generator->randomElement($users))
                ->setLieu($generator->randomElement($lieu))
                ->addUser($generator->randomElement($users));
            $manager -> persist($sortie);
        }
        $manager->flush();

    }


    public function addVille(ObjectManager $manager){

        $generator = Factory::create('fr_FR');


        for ($i = 0; $i < 10; $i++){
            $ville = new Ville();
            $ville
                ->setName($generator->city)
                ->setCp($generator->postcode);
            $manager -> persist($ville);
        }
        $manager->flush();

    }

    public function addLieu(ObjectManager $manager, VilleRepository $villeRepository){

        $generator = Factory::create('fr_FR');

        $ville = $villeRepository->findAll();

        for ($i = 0; $i < 10; $i++){
            $lieu = new Lieu();
            $lieu
                ->setNom($generator->name)
                ->setRue($generator->streetName)
                ->setVille($generator->randomElement($ville))
                ->setLatitude($generator->randomFloat(8, 30, 50))
                ->setLongitude($generator->randomFloat(8, 0, 10));
            $manager -> persist($lieu);
        }
        $manager->flush();
    }



}
