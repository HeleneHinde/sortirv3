<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\CampusType;

use App\Form\RegistrationFormType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use App\Security\AppAuthenticator;
use App\Tools\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{


    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'dash')]
    public function dash(Request $request, CampusRepository $campusRepository): Response
    {


        return $this->render('admin/dashboard.html.twig');
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/campus', name: 'campus_list')]
    public function listCampus(Request $request, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();

        $campu = new Campus();
        $campuForm = $this->createForm(CampusType::class, $campu);
        $campuForm->handleRequest($request);

        if ($campuForm->isSubmitted() && $campuForm->isValid()) {
            $campusRepository->add($campu, true);

            //après ajout, redirige vers la page de détail
            $this->addFlash('success', 'Campus ajouté !');
            return $this->redirectToRoute('admin_campus_list');

        }

        return $this->render('admin/adcampus.html.twig', [
            'campus' => $campus,
            'campuForm' => $campuForm->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/campus/update/{id}', name: 'campus_update', requirements: ["id" => "\d+"])]
    public function upCampus(Request $request, int $id, CampusRepository $campusRepository): Response
    {

        //affichage des campus
        $campus = $campusRepository->find($id);


        $campuForm = $this->createForm(CampusType::class, $campus);
        $campuForm->handleRequest($request);

        if ($campuForm->isSubmitted() && $campuForm->isValid()) {

            $campusRepository->add($campus, true);

            //après ajout, redirige vers la page de détail

            $this->addFlash('success', 'Campus modifié !');

            return $this->redirectToRoute('admin_campus_list');
        }
        return $this->render('admin/adcampus.html.twig', [
            'campus' => $campus,
            'campuForm' => $campuForm->createView(),
        ]);

    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/campus/delete/{id}', name: 'campus_delete', requirements: ["id" => "\d+"])]
    public function delCampus(int $id, CampusRepository $campusRepository): Response
    {
        //on récupère le campus à supprimer
        $campus = $campusRepository->find($id);

        //on supprime la série
        $campusRepository->remove($campus, true);

        $this->addFlash('success', $campus->getName() . ' a été supprimé !');


        return $this->redirectToRoute('admin_campus_list');

    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/user/add', name: 'user_add')]
    public function addUser(Uploader $uploader,Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm( RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $roles[]=$form->get('roles')->getData();

            $user->setRoles($roles);


            //compare les 2 Passwords
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $filePhoto=$form->get('photo')->getData();


            if($filePhoto){
                $name = $user->getUsername();
                $directory='img/user';
                $newFileName=$uploader->save($filePhoto,$name,$directory);
                $user->setPhoto($newFileName);

            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Etudiant ajouté !');

            return $this->redirectToRoute('admin_user_list');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/user/list', name: 'user_list')]
    public function listUser(UserRepository $userRepository): Response
    {
        $users=$userRepository->findAll();


        return $this->render('admin/listuser.html.twig', [
            'users' => $users
        ]);



    }


    #[IsGranted("ROLE_ADMIN")]
    #[Route('/user/delete/{id}', name: 'user_delete', requirements: ["id" => "\d+"])]
    public function delete(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        $userRepository->remove($user, true);

        return $this->redirectToRoute('admin_user_list');
    }


    #[IsGranted("ROLE_ADMIN")]
    #[Route('/ville', name: 'ville_list')]
    public function listVille(VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findAll();


        return $this->render('admin/listVilles.html.twig', [
            'villes' => $villes
        ]);



    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/ville/create', name: 'ville_create')]
    public function createVille(VilleRepository $villeRepository, Request $request): Response
    {
        $ville = new Ville();
        $villeForm = $this->createForm(VilleType::class, $ville);

        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()){
            $villeRepository->add($ville, true);
            $this->addFlash('succès', 'Ville  "'. $ville->getName() .'" ajoutée avec succès');

            return $this->redirectToRoute('admin_ville_list');
        }


        return $this->render('admin/createVille.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/ville/{id}', name: 'ville_show', requirements: ["id" => "\d+"])]
    public function showVille(VilleRepository $villeRepository, int $id): Response
    {
        $ville = $villeRepository->find($id);



        return $this->render('admin/showVille.html.twig', [
            'ville' => $ville
        ]);
    }



    #[IsGranted("ROLE_ADMIN")]
    #[Route('/ville/update/{id}', name: 'ville_update', requirements: ["id" => "\d+"])]
    public function updateVille(VilleRepository $villeRepository, Request $request, int $id): Response
    {
        $ville = $villeRepository->find($id);
        $villeForm = $this->createForm(VilleType::class, $ville);

        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()){
            $villeRepository->add($ville, true);
            $this->addFlash('succès', 'Ville  "'. $ville->getName() .'" modifiée avec succès');

            return $this->redirectToRoute('admin_ville_list');
        }


        return $this->render('admin/updateVille.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }
}
