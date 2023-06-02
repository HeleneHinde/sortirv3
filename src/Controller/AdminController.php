<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Form\CampusType;

use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
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
                $form->get('confirmPassword')->addError(new FormError('Passwords do not match.'));
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

            return $this->redirectToRoute('admin_campus_list');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }

    #[Route('/user/list', name: 'user_list')]
    public function listUser(UserRepository $userRepository): Response
    {
        $users=$userRepository->findAll();


        return $this->render('admin/listuser.html.twig', [
            'users' => $users
        ]);



    }


    #[Route('/user/delete/{id}', name: 'user_delete', requirements: ["id" => "\d+"])]
    public function delete(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        $userRepository->remove($user, true);

        return $this->redirectToRoute('admin_user_list');
    }

}
