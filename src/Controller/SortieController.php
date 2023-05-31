<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {

        $sortie = new Sortie();


        return $this->render('sortie/create.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}
