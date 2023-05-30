<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/{id}', name: 'show', requirements: ["page" => "\d+"])]
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

    #[Route('/update/{id}', name: 'update', requirements: ["page" => "\d+"])]
    public function update(int $id, UserRepository $userRepository): Response
    {
/*        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException("Oop ! User not found !");
        }


        return $this->render('user/profil.html.twig', [
            'user'=>$user,
        ]);*/
    }
}
