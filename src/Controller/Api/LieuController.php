<?php

namespace App\Controller\Api;

use App\Controller\MainController;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/lieu', name: 'api_lieu_')]
class LieuController extends AbstractController
{
    #[Route('', name: 'retrieve_all', methods: ['GET'])]
    public function retrieveAll(LieuRepository $lieuRepository): Response
    {
        $lieux = $lieuRepository->findAll();
        return $this->json($lieux, 200, [], ['groups' => 'lieu_data']);
    }


    #[Route('/{id}', name: 'retrieve_one', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function retrieveOne(int $id, LieuRepository $lieuRepository): Response
    {
        $lieu=$lieuRepository->find($id);
        if($lieu){
            return $this->json(['sortie' => $lieu], 200, [], ['groups' => 'lieu_data']);
        }
        return $this->json(['error' => 'Lieu not found']);
    }


}