<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortiesType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu_')]
class LieuController extends AbstractController
{
    #[Route('/', name: 'list')]
    #[IsGranted("ROLE_ADMIN")]
    public function list(LieuRepository $lieuRepository): Response
    {
        $lieux = $lieuRepository->findAll();


        return $this->render('lieu/list.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_ADMIN")]
    public function show(LieuRepository $lieuRepository, int $id): Response
    {
        $lieu = $lieuRepository->find($id);


        return $this->render('lieu/show.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(LieuRepository $lieuRepository, int $id): Response
    {
        $lieu = $lieuRepository->find($id);

        $lieuRepository->remove($lieu, true);

        $this->addFlash('success', $lieu->getNom() . ' a été supprimé !');

        return $this->redirectToRoute('lieu_list');
    }

    #[Route('/create', name: 'create')]
    #[IsGranted("ROLE_USER")]
    public function create(LieuRepository $lieuRepository, VilleRepository $villeRepository, Request $request): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);

        $lieuForm->handleRequest($request);


        if ($lieuForm->isSubmitted() && $lieuForm->isValid()){
            if ($request->request->get('myCheckboxVille')){
                $ville = new Ville();
                $ville->setName($request->request->get('lieu')['Ville']);
                $ville->setCp($request->request->get('lieu')['Code_postal']);

                $villeRepository->add($ville, true);

                $lieu->setVille($ville);

            } else {
                $lieu->setVille($villeRepository->find($lieuForm->get('ville')->getData()));
            }

            $lieuRepository->add($lieu, true);



            $this->addFlash('success', "Lieu ". $lieu->getNom() .' ajouté avec succès');


            return $this->redirectToRoute('lieu_list');
        }

        return $this->render('lieu/create.html.twig', [
            'lieuForm' => $lieuForm->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(LieuRepository $lieuRepository, VilleRepository $villeRepository,Request $request, int $id): Response
    {
        $lieu = $lieuRepository->find($id);

        $lieuForm2 = $this->createForm(LieuType::class, $lieu);

        $lieuForm2->handleRequest($request);


        if ($lieuForm2->isSubmitted() && $lieuForm2->isValid()){

            if ($request->request->get('myCheckboxVille')){
                $ville = new Ville();
                $ville->setName($request->request->get('lieu')['Ville']);
                $ville->setCp($request->request->get('lieu')['Code_postal']);

                $villeRepository->add($ville, true);

                $lieu->setVille($ville);

            } else {
                $lieu->setVille($villeRepository->find($lieuForm2->get('ville')->getData()));
            }

            $lieuRepository->add($lieu, true);

            $this->addFlash('succès', 'Lieu "'. $lieu->getNom() .'" modifié avec succès');


            return $this->redirectToRoute('lieu_list');
        }

        return $this->render('lieu/edit.html.twig', [
            'lieuForm' => $lieuForm2->createView(),
            'lieu' => $lieu
        ]);
    }
}
