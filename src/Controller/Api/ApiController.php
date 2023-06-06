<?php

namespace App\Controller\Api;

use App\Repository\SerieRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/sortie', name: 'api_sortie_')]
class ApiController extends AbstractController
{
    #[Route('', name: 'retrieve_all', methods: ['GET'])]
    public function retrieveAll(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();

        return $this->json($sorties, 200, [], ['groups' => 'sortie_data']);
    }


    #[Route('/{id}', name: 'retrieve_one', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function retrieveOne(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie=$sortieRepository->find($id);
        if($sortie){
            return $this->json(['sortie' => $sortie]);
        }
        return $this->json(['error' => 'Sortie not found']);
    }

    #[Route('/{id}', name: 'delete_one', methods: ['DELETE'])]
    public function deleteOne(int $id): Response
    {
    }

    #[Route('/{id}', name: 'update_one', methods: ['PUT'])]
    public function updateOne(int $id, \Symfony\Component\HttpFoundation\Request $request, SortieRepository $sortieRepository): Response
    {
        $sortie=$sortieRepository->find($id);
        if($sortie){


            return $this->json(['sortie' => $sortie]);
        }
        return $this->json(['error' => 'Sortie not found']);
    }

}