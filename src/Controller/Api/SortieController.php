<?php

namespace App\Controller\Api;

use App\Controller\MainController;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/sortie', name: 'api_sortie_')]
class SortieController extends AbstractController
{
    #[Route('', name: 'retrieve_all', methods: ['GET'])]
    public function retrieveAll(EtatRepository $etatRepository, SortieRepository $sortieRepository): Response
    {
        $etatH= $etatRepository->find(MainController::HISTORISE);
        $etatC= $etatRepository->find(MainController::CREATE);
        $sorties = $sortieRepository->main($etatH, $etatC);

        return $this->json($sorties, 200, [], ['groups' => 'sortie_data']);
    }


    #[Route('/{id}', name: 'retrieve_one', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function retrieveOne(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie=$sortieRepository->find($id);
        if($sortie){
            return $this->json(['sortie' => $sortie], 200, [], ['groups' => 'sortie_data']);
        }
        return $this->json(['error' => 'Sortie not found']);
    }


}