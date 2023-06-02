<?php

namespace App\Controller;

use DateTime;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieMainType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
     const HISTORISE=7;
    const CREATE=1;

    #[Route('/', name: 'main_home')]
    public function home(EntityManagerInterface $entityManager, Request $request, SortieRepository $sortieRepository, CampusRepository $campusRepository, Security $security, UserRepository $userRepository, EtatRepository $etatRepository ): Response
    {

        $campus = $campusRepository->findAll();

        //code pour l'historisation des sorties : on récupère l'état historisée, on récupère l'ensemble des sorties
        $etat= $etatRepository->find(self::HISTORISE);

        $sortieH=$sortieRepository->findAll();

        $currentDate = new \DateTime();
        $limitDate = $currentDate->modify('-30 days');

        //si la date de sortie est antérieure à la date d'aujourd'hui + 30 jours, alors la sortie passe à l'état historisée
        foreach ($sortieH as &$s) {
            $firstAirDate = $s->getFirstAirDate();

            if ($firstAirDate < $limitDate) {
                $s->setEtat($etat);
                $entityManager->persist($s);
            }

        }
        $entityManager->flush();



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

            $etat= $etatRepository->find(self::HISTORISE);
            $sorties = $sortieRepository->mainSearch($name, $dateUn, $dateDeux, $campu, $userIdScales, $userIdHorns, $userIdHornsNR, $dateDuJour, $etat);


        } else {
            $etatH= $etatRepository->find(self::HISTORISE);
            $etatC= $etatRepository->find(self::CREATE);
            $sorties = $sortieRepository->main($etatH, $etatC);
        }


        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'campus' => $campus,
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
