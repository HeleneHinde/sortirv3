<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class Sortiev2Controller extends AbstractController
{
    #[Route('/sortiev2/{id}', name: 'sortiev2_show',requirements: ['id'=> '\d+'] )]
    #[IsGranted("ROLE_USER")]
    public function show(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie =$sortieRepository->find($id);

        if(!$sortie){
            //permet de lancer une erreur 404
            throw $this->createNotFoundException("Oups !! Sortie not found");
        }

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie
        ]);
    }



    #[Route('/delete/{id}', name: 'sortie_delete',requirements: ['id'=> '\d+'])]
    #[IsGranted("ROLE_USER")]
    public function delete(int $id, SortieRepository $sortieRepository, Security $security)
    {
        $user=$security->getUser();
        $sortie = $sortieRepository->find($id);
        if ($sortie->getUser()==$user){



        $sortieRepository->remove($sortie, true);

        $this->addFlash('success', "la sortie à ".$sortie->getName()." a été supprimé !");

        return $this->redirectToRoute('main_home');

        }
        $this->addFlash('error', "Vous n'êtes pas l'organisateur de cette sortie !");
        return $this->redirectToRoute('main_home');

    }




}
