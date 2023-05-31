<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\CampusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture

{
    private UserPasswordHasherInterface $hasher;

    private CampusRepository $campusRepository;

    public function __construct(UserPasswordHasherInterface $hasher, CampusRepository $campusRepository)
    {
        $this->hasher=$hasher;
        $this->campusRepository=$campusRepository;

    }

    public function load(ObjectManager $manager): void
    {
        $this->addUsers($manager, $this->campusRepository);
    }
    public function addUsers(ObjectManager $manager, CampusRepository $campusRepository):void {


        $generator = Factory::create('fr_FR');

        $campus=$campusRepository->findAll();


        /*for ($i=0;$i<10;$i++){

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

        }*/
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

    public function addSortie(ObjectManager $manager): void
    {



    }




}
