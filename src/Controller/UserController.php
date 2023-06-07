<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use App\Tools\Uploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
            'user' => $user,
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ["id" => "\d+"])]
    public function update(int     $id,
                           Request $request, UserRepository $userRepository, Uploader $uploader, Security $security): Response
    {
        $user = $userRepository->find($id);

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            if ($request->request->has("actif")) {
                $user->setActif(!$user->isActif());
            }
            $user1 = $security->getUser();
            $roleUser = new ArrayCollection($user1->getRoles());
            if ($roleUser->contains('ROLE_ADMIN')) {

                $roles[] = $userForm->get('roles')->getData();
                if ($roles) {
                    $user->setRoles($roles);
                }
            }

            $plainPassword = $userForm->get('plainPassword')->getData();
            $confirmPassword = $userForm->get('confirmPassword')->getData();

            if ($plainPassword !== $confirmPassword) {
                $userForm->get('confirmPassword')->addError(new FormError('Les mots de passe ne correspondent pas.'));
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $userForm->createView(),
                ]);
            }


            $filePhoto = $userForm->get('photo')->getData();


            if ($filePhoto) {
                $name = $user->getUsername();
                $directory = 'img/user';
                $newFileName = $uploader->save($filePhoto, $name, $directory);
                $user->setPhoto($newFileName);

            }

            $userRepository->save($user, true);

            //après ajout, redirige vers la page de détail

            if ($request->request->has("actif")) {
                $this->addFlash('success', 'Profil mis à jour');
                return $this->redirectToRoute('admin_user_list');
            } else {
                $this->addFlash('success', 'Votre profil a été mis à jour !');
                return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
            }
        }

        return $this->render('user/update.html.twig', [
            'userForm' => $userForm->createView(),
            'user' => $user
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ["id" => "\d+"])]
    public function delete(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        $userRepository->remove($user, true);

        return $this->render('main/home.html.twig');
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/admin/import', name: 'admin_import')]
    public function importUser(Request $request, CampusRepository $campusRepository, UserRepository $userRepository, Security $security, Uploader $uploader, EntityManagerInterface $entityManager)
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();


            $csv = fopen($file, 'r');
            while (!feof($csv)) {
                $line[] = fgetcsv($csv, 1024);
            }
            fclose($csv);

            foreach ($line as &$row) {
                if (is_array($row)) {
                    $user1 = new User();
                    $campus = $campusRepository->find($row[1]);
                    $user1->setCampus($campus);
                    $user1->setUsername($row[2]);
                    $res = str_replace(array('[', ']', '"'), '', $row[3]);
                    $user1->setRoles([$res]);
                    $user1->setPassword($row[4]);
                    $user1->setLastname($row[6]);
                    $user1->setFirstname($row[5]);
                    $user1->setPhoneNumber($row[7]);
                    $user1->setEmail($row[8]);
                    $user1->setPhoto($row[9]);
                    $user1->setActif($row[10]);

                    $entityManager->persist($user1);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/importuser.html.twig', [
            'form' => $form->createView(),
        ]);

    }



}
