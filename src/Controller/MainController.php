<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieMainType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function home(Request $request,SortieRepository $sortieRepository, CampusRepository $campusRepository): Response
    {
        $campus=$campusRepository->findAll();

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieMainType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $name = $sortieForm->get('name')->getData();
            $dateUn=$sortieForm->get('dateUn')->getData();
            $dateDeux=$sortieForm->get('dateDeux')->getData();
            $campu=$sortieForm->get('campus')->getData();
            $orga=$sortieForm->get('scales')->getData();
            $horns=$sortieForm->get('horns')->getData();
            $hornsNR=$sortieForm->get('horns_not_registered')->getData();
            $past=$sortieForm->get('past_outings')->getData();



        }

        $sorties = $sortieRepository->findAll();

        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'campus'=>$campus,
            'sortieForm'=>$sortieForm->createView()
        ]);
    }
}
