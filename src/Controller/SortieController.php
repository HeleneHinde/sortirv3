<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortiesType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();


        return $this->render('sortie/list.html.twig', [
            'sorties' => $sorties,
        ]);
    }


    #[Route('/create', name: 'create')]
    public function create(Request $request, SortieRepository $sortieRepository, CampusRepository $campusRepository): Response
    {

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortiesType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $sortie->setCampus($this->getUser()->getCampus()->getName());

            $sortieRepository->add($sortie);

            $this->addFlash('succès', 'Sortie ajoutée avec succès');


//            return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
            return $this->redirectToRoute('main_home');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }



}
