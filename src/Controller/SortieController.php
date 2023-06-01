<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortiesType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;

use App\Repository\SortieRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
        ]);
    }


    #[Route('/create', name: 'create')]
    public function create(Request $request,SortieRepository $sortieRepository): Response
    {

      //  dd("toto");
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortiesType::class, $sortie);

        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $sortie->setCampus($this->getUser()->getCampus());
            $sortie->setUser($this->getUser());
            if ($request->request->get('myCheckbox')){
                $lieu = new Lieu();
                $data = $sortieForm->getData();
                $lieu->setRue($data['rue']);
                $lieu->setNom($sortieForm->get('nom_lieu')->getData());
                $lieu->setLatitude($sortieForm->get('Latitude')->getData());
                $lieu->setLongitude($sortieForm->get('Longitude')->getData());
                if ($request->request->get('myCheckboxVille')){
                    $ville = new Ville();
                    $ville->setName($sortieForm->get('Ville')->getData());
                    $ville->setCp($sortieForm->get('Code_postal')->getData());
                    $lieu->setVille($ville);
                } else {
                    $lieu->setVille($sortieForm->get('ville_select')->getData());
                }
                $sortie->setLieu($lieu);
            } else {
                $sortie->setLieu($sortieForm->get('lieu')->getData());
            }
            $sortieRepository->add($sortie, true);

            $this->addFlash('succès', 'Sortie ajoutée avec succès');


//            return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }




//    #[Route('/', name: 'list')]
//    public function list(SortieRepository $sortieRepository): Response
//    {
//
//        $sorties = $sortieRepository->findAll();
//
//        return $this->render('sortie/list.html.twig', [
//            'sorties' => $sorties,
//        ]);
//    }




}
