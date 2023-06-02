<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieMainType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class InscriptionController extends AbstractController
{
    #[Route('/inscription/{id}', name: 'inscription_user', requirements: ['id'=> '\d+'])]
    public function inscription(Security $security, int $id, SortieRepository $sortieRepository, CampusRepository $campusRepository, EtatRepository $etatRepository): Response
    {

        //récupère l'utilisateur de la session
        $user = $security->getUser();

        //récupère la sortie
        $sortie = $sortieRepository->find($id);
        $etat = $etatRepository->findOneByName('Ouverte');

        //si l'user fait déjà partie de la sortie, alors ça le désincrit, sinon ça l'inscrit
        if ($sortie->getUsers()->contains($user)) {
            $sortie->removeUser($user);
        } elseif ($sortie->getEtat() === $etat) {
            $sortie->addUser($user);
        }

        $sortieRepository->add($sortie, true);

        //code pour régénerer la vue Main Home
        $sortiee = new Sortie();
        $sortieForm = $this->createForm(SortieMainType::class, $sortiee);

        $sorties = $sortieRepository->findAll();
        $campus = $campusRepository->findAll();

        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'campus' => $campus,
            'sortieForm' => $sortieForm->createView(),
        ]);
    }
}
