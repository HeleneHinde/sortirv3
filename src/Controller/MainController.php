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
            $dateUn = $sortieForm->get('dateUn')->getData();
            $dateDeux = $sortieForm->get('dateDeux')->getData();
            $campu = $sortieForm->get('campus')->getData();
            $orga = $sortieForm->get('scales')->getData();
            $horns = $sortieForm->get('horns')->getData();
            $hornsNR = $sortieForm->get('horns_not_registered')->getData();
            $past = $sortieForm->get('past_outings')->getData();

            // Initialisation des variables pour les filtres de recherche

            $userIdScales = null;
            $userIdHorns = null;
            $userIdHornsNR = null;
            $dateDuJour = null;

            if (isset($orga['scales']) && $orga['scales']) {
                // Récupérer l'ID de l'utilisateur pour le filtre "scales"
                $userIdScales = $sortieForm->get('userId')->getData();
            }

            if (isset($horns['horns']) && $horns['horns']) {
                // Récupérer l'ID de l'utilisateur pour le filtre "horns"
                $userIdHorns = $sortieForm->get('userId')->getData();
            }

            if (isset($hornsNR['horns_not_registered']) && $hornsNR['horns_not_registered']) {
                // Récupérer l'ID de l'utilisateur pour le filtre "horns_not_registered"
                $userIdHornsNR = $sortieForm->get('userId')->getData();
            }

            if (isset($past['past_outings']) && $past['past_outings']) {
                // Définir la date du jour pour le filtre "past_outings"
                $dateDuJour = date('Ymd');
            }


            dd($userIdScales);

            $sorties=$sortieRepository->mainSearch($name, $dateUn, $dateDeux, $campu, $userIdScales, $userIdHorns, $userIdHornsNR, $dateDuJour);





        } else {
            $sorties = $sortieRepository->findAll();
        }



        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'campus'=>$campus,
            'sortieForm'=>$sortieForm->createView()
        ]);
    }
}
