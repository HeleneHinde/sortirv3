<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieMainType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function home(Request $request, SortieRepository $sortieRepository, CampusRepository $campusRepository, Security $security, UserRepository $userRepository): Response
    {
        $campus = $campusRepository->findAll();

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieMainType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

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

            $user1 = $security->getUser();


            if ($orga) {
                // Récupérer l'ID de l'utilisateur pour le filtre "scales"
                if ($user1) {
                    // Récupérer l'ID de l'utilisateur
                    $userId = $user1->getId();
                    $userIdScales=$userRepository->find($userId);

                    // Utilisez $userId selon vos besoins
                }
            }

            if ($horns) {
                if ($user1) {
                    // Récupérer l'ID de l'utilisateur pour le filtre "horns"
                    $userId = $user1->getId();
                    $userIdHorns = $userRepository->find($userId);
                }
            }

            if ($hornsNR) {
                if ($user1) {

                    // Récupérer l'ID de l'utilisateur pour le filtre "horns_not_registered"
                    $userId = $user1->getId();
                    $userIdHornsNR = $userRepository->find($userId);
                }
            }

            if ($past) {
                // Définir la date du jour pour le filtre "past_outings"
                $dateDuJour = date('Ymd');
            }


            $sorties = $sortieRepository->mainSearch($name, $dateUn, $dateDeux, $campu, $userIdScales, $userIdHorns, $userIdHornsNR, $dateDuJour);


        } else {
            $sorties = $sortieRepository->findAll();
        }


        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'campus' => $campus,
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
