<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Sortiev2Controller extends AbstractController
{
    #[Route('/sortiev2/{id}', name: 'sortiev2_show')]
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
    public function delete(int $id, SortieRepository $sortieRepository)
    {
        $sortie = $sortieRepository->find($id);

        $sortieRepository->remove($sortie, true);

        $this->addFlash('success', $sortie->getName()."has been removed !");

        return $this->redirectToRoute('main_home');

    }




}
