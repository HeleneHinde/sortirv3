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

use App\Repository\LieuRepository;
use App\Repository\SortieRepository;

use App\Repository\VilleRepository;
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
    public function create(Request $request,SortieRepository $sortieRepository, VilleRepository $villeRepository, EtatRepository $etatRepository, LieuRepository $lieuRepository): Response
    {

        //  dd("toto");
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortiesType::class, $sortie);


        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            if ($request->request->has("publiee")){
                $sortie->setEtat($etatRepository->find(2));
            } else {
                $sortie->setEtat($etatRepository->find(1));
            }
            $sortie->setCampus($this->getUser()->getCampus());
            $sortie->setUser($this->getUser());
            if ($request->request->get('myCheckbox')){
                $lieu = new Lieu();
                $data = $sortieForm->getData();
                $lieu->setNom($request->request->get('sorties')['name']);
                $lieu->setRue($request->request->get('sorties')['rue']);
                $lieu->setLatitude($request->request->get('sorties')['Latitude']);
                $lieu->setLongitude($request->request->get('sorties')['Longitude']);
                $lieu->setVille($villeRepository->find($request->request->get('sorties')['ville_select']));

                $lieuRepository->add($lieu, true);

                $sortie->setLieu($lieu);
            } else {
                $sortie->setLieu($lieuRepository->find($sortieForm->get('lieu')->getData()));
            }
            $sortie->getUsers()->add($this->getUser());
            $sortieRepository->add($sortie, true);

            $this->addFlash('succès', 'Sortie ajoutée avec succès');


            return $this->redirectToRoute('sortiev2_show', ['id' => $sortie->getId()]);
//            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ['id'=> '\d+'])]
    public function update(Request $request,SortieRepository $sortieRepository, VilleRepository $villeRepository, EtatRepository $etatRepository, LieuRepository $lieuRepository, int $id): Response
    {

        //  dd("toto");
        $sortie = $sortieRepository->find($id);
        $sortieForm = $this->createForm(SortiesType::class, $sortie);

        $sortieForm->handleRequest($request);
        if ($sortie->getEtat()->getId() >= 3){
            $this->addFlash('Erreur', 'Vous ne pouvez plus modifier cette sortie');
            return $this->redirectToRoute('main_home');
        }

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            if ($request->request->has("annuler")){
                if ($request->request->get('motif_annulation') != null){
                    $sortie->setEtat($etatRepository->find(6));
                    $sortie->setInfosSortie($sortie->getInfosSortie(). ' <br>SORTIE ANNULÉ : <br>' . $request->request->get('motif_annulation'));
                } else {
                    return $this->redirectToRoute('sortie_update', ['id' => $sortie->getId()]);
                }
            }else if ($request->request->has("publiee")){
                $sortie->setEtat($etatRepository->find(2));
            }else if ($sortie->getEtat()->getId() != 2){
                $sortie->setEtat($etatRepository->find(1));
            }
            $sortie->setCampus($this->getUser()->getCampus());
            $sortie->setUser($this->getUser());
            if ($request->request->get('myCheckbox')){
                $lieu = new Lieu();
                $lieu->setNom($request->request->get('sorties')['nom_lieu']);
                $lieu->setRue($request->request->get('sorties')['rue']);
                $lieu->setLatitude($request->request->get('sorties')['Latitude']);
                $lieu->setLongitude($request->request->get('sorties')['Longitude']);
                $lieu->setVille($villeRepository->find($request->request->get('sorties')['ville_select']));

                $lieuRepository->add($lieu, true);

                $sortie->setLieu($lieu);
            } else {
                $sortie->setLieu($lieuRepository->find($sortieForm->get('lieu')->getData()));
            }
            $sortieRepository->add($sortie, true);

            $this->addFlash('Success', 'Sortie modifiée avec succès');


            return $this->redirectToRoute('sortiev2_show', ['id' => $sortie->getId()]);
//            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/update.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie
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
