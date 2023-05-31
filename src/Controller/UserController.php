<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Tools\Uploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/{id}', name: 'show', requirements: ["id" => "\d+"])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException("Oop ! User not found !");
        }


        return $this->render('user/profil.html.twig', [
            'user'=>$user,
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ["id" => "\d+"])]
    public function update(int $id,
                          Request $request, UserRepository $userRepository,Uploader $uploader): Response
    {
        $user = $userRepository->find($id);

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $plainPassword = $userForm->get('plainPassword')->getData();
            $confirmPassword = $userForm->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $userForm->get('confirmPassword')->addError(new FormError('Passwords do not match.'));
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $userForm->createView(),
                ]);
            }


            $filePhoto=$userForm->get('photo')->getData();


            if($filePhoto){
                $name = $user->getUsername();
                $directory='img/user';
                $newFileName=$uploader->save($filePhoto,$name,$directory);
                $user->setPhoto($newFileName);

            }

            $userRepository->save($user, true);

            //après ajout, redirige vers la page de détail
            $this->addFlash('success', 'Votre profil a été mis à jour !');
            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/update.html.twig', ['userForm' => $userForm->createView()]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ["id" => "\d+"])]
    public function delete(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        $userRepository->remove($user, true);

        return $this->render('main/home.html.twig');
    }

}
